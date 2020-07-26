<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Http\Resources\UserResource;

use App\Http\Requests\UserRequest;
use App\Http\Controllers\BaseController as BaseController;
use Carbon\Carbon;
use Validator;

use App\User;

class RegisterController extends BaseController
{
    /**
     * @OA\Post(
     *     tags={"User"},
     *     summary="Register a user",
     *     description="Register a new user",
     *     path="/api/register",
     *
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="name", type="string"),
     *              @OA\Property(property="lastname", type="string"),
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="codename", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="c_password", type="string")
     *          )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent()
     *     ),
     *
     * ),
     *
    */
    public function register(UserRequest $request)
    {
        $validated = $request->validated();

        // create user
        $user = User::create($validated);

        // create save data
        $user->save_data()->create(['value' => [
            "total_points" => 0,
            "level" => 1
        ]]);

        // Create response
        $success = [
            'token' => $user->createToken(env('APP_NAME', 'AppName'))->accessToken,
            'user' => new UserResource($user)
        ];

        return $this->sendResponse($request, $success, __("User created successfully!"));
    }

    /**
     * @OA\Post(
     *     tags={"User"},
     *     summary="Authenticate a user",
     *     description="Authenticates a user, and returns the registration data",
     *     path="/api/login",
     *
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string"),
     *              @OA\Property(property="codename", type="string"),
     *              @OA\Property(property="password", type="string"),
     *          )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent()
     *     ),
     *
     * ),
     *
    */
    public function login(Request $request)
    {

        $credentials = $request->only('codename', 'email', 'password');

        if(Auth::attempt($credentials)){

            $user = Auth::user();

            $success = [
                'token' => $user->createToken(env('APP_NAME', 'AppName'))->accessToken,
                'user' => new UserResource($user)
            ];

            return $this->sendResponse($request, $success, __("User authenticated successfully!"));
        }
        else{
            return $this->sendError($request, [], "user_not_found", __("User not found!"));
        }
    }

    /**
     * @OA\Post(
     *     tags={"User"},
     *     summary="Forgot the password",
     *     description="Send a code for the user to be able to create a new password",
     *     path="/api/forgot_password",
     *
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string")
     *          )
     *     ),

     *     @OA\Response(response="200", description="", @OA\JsonContent()),
     * ),
     *
    */
    public function forgotPassword(Request $request)
    {
        $req = $request->only('email');
        $email = $req['email'];

        $user = User::where("email", $email)->first();

        if (empty($user))
        {
            return $this->sendError($request, [], "user_not_found", __("User not found!"));
        }

        $id = $user->id;
        $nomeCompleto = $user->full_name;


        $faker = \Faker\Factory::create();
        $pinRule = '[@$*:ABCDEFGHJKMNPQRSTUVWXZYabcdeghkmnpqsuvwxyz2-9]{6}';
        $randomCode = $faker->regexify($pinRule);

        $passwordExpires = Carbon::now()->addMinutes(10);
        $passwordExpires->tz('UTC');

        $user->passwordExpires = $passwordExpires;
        $user->passwordNew = $randomCode;
        $user->save();

        if (!empty(env('MAIL_HOST')))
        {
            Mail::send('emails.reminder', ['user' => $user, 'code' => $randomCode], function ($m) use ($user) {
                $m->from(env('MAIL_USERNAME'), env('MAIL_NAME'));
                $m
                ->to($user->email, $user->full_name)
                ->subject(env('MAIL_SUBJECT') . ' ' . __("Create new password"));
            });
        }


        return $this->sendResponse($request, [
            "code" => $randomCode
        ], __("We sent an email with a code to create a new password!"));
    }


    /**
     * @OA\Put(
     *     tags={"User"},
     *     summary="Create new password",
     *     description="Creates a new password with previously entered code",
     *     path="/api/reset_password",
     *
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", type="string"),
     *              @OA\Property(property="password", type="string"),
     *              @OA\Property(property="c_password", type="string"),
     *          )
     *     ),

     *     @OA\Response(response="200", description="", @OA\JsonContent()),
     * ),
     *
    */
    public function resetPassword(Request $request){

        $req = $request->input();

        $password = $req['password'];
        $c_password = $req['c_password'];
        $code = $req['code'];

        $user = User::where("passwordNew", $code)->first();

        if (empty($user)){
            return $this->sendError($request, [], "code_not_found", __("Code not found!"));

        }else{
            $passExpired = Carbon::now()->greaterThan(Carbon::createFromFormat("Y-m-d H:i:s",$user->passwordExpires));
            if ($passExpired){
                return $this->sendError($request, [], "user_not_found", __("The code used is no longer valid."));
            }
            if (empty($password) || empty($c_password)){
                return $this->sendError($request, [], "user_not_found", __("Fill in all fields"));
            }
            if ($password != $c_password){
                return $this->sendError($request, [], "user_not_found", __("Passwords must be the same"));
            }

            $user->password = $password;
            $user->passwordNew = null;
            $user->passwordExpires = null;
            $user->save();

            // Revoke tokens
            $userTokens = $user->tokens;
            foreach($userTokens as $token) {
                $token->revoke();
                $token->delete();
            }
        }

        if (!empty(env('MAIL_HOST')))
        {
            Mail::send('emails.newpassword', ['user' => $user], function ($m) use ($user) {
                $m->from(env('MAIL_USERNAME'), env('MAIL_NAME'));
                $m
                    ->to($user['email'], $user['name'])
                    ->subject(env('MAIL_SUBJECT') . ' ' . __("New password created successfully!"));
            });
        }

        return $this->sendResponse($request, [], __("New password created successfully!"));
    }

}
