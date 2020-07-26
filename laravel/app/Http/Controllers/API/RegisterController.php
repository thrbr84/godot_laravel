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
     *     summary="Insere um usuário",
     *     description="Insere um usuário na API",
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
     *         description="Usuário criado com sucesso!",
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

        return $this->sendResponse($request, $success, "Usuário criado com sucesso!");
    }

    /**
     * @OA\Post(
     *     tags={"User"},
     *     summary="Autentica um usuário",
     *     description="Autentica um usuário, e retorna os dados de cadastro",
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
     *         description="Usuário autenticado com sucesso!",
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

            return $this->sendResponse($request, $success, "Usuário autenticado com sucesso!");
        }
        else{
            return $this->sendError($request, [], "user_not_found", "Usuário não encontado!");
        }
    }

    /**
     * @OA\Post(
     *     tags={"User"},
     *     summary="Esqueci senha",
     *     description="Envia um código para o usuário conseguir criar nova senha",
     *     path="/api/forgot_password",
     *
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="email", type="string")
     *          )
     *     ),

     *     @OA\Response(response="200", description="Código enviado por email", @OA\JsonContent()),
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
            return $this->sendError($request, [], "user_not_found", "Ops! Esse e-mail não tem acesso!");
        }

        if ($user->active === 0)
        {
            return $this->sendError($request, [], "user_inactive", "Ops! Esse e-mail está inativo!");
        }


        $id = $user->id;
        $nomeCompleto = $user->full_name;

        $randomCode = Session::getId();
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
                ->subject(env('MAIL_SUBJECT') . ' Criar nova senha');
            });
        }


        return $this->sendResponse($request, [
            "code" => $randomCode
        ], "Enviamos um e-mail com um código para criar uma nova senha!");
    }


    /**
     * @OA\Put(
     *     tags={"User"},
     *     summary="Criar nova senha",
     *     description="Cria uma nova senha com código informado anteriormente",
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

     *     @OA\Response(response="200", description="Código enviado por email", @OA\JsonContent()),
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
            return $this->sendError($request, [], "user_not_found", "User not found");

        }else{
            $passExpired = Carbon::now()->greaterThan(Carbon::createFromFormat("Y-m-d H:i:s",$user->passwordExpires));
            if ($passExpired){
                return $this->sendError($request, [], "user_not_found", "O código utilizado não é mais válido.");
            }
            if (empty($password) || empty($c_password)){
                return $this->sendError($request, [], "user_not_found", "Preencha todos os campos");
            }
            if ($password != $c_password){
                return $this->sendError($request, [], "user_not_found", "As senhas precisam ser iguais");
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
                    ->subject(env('MAIL_SUBJECT') . ' Nova senha criada com sucesso!');
            });
        }

        return $this->sendResponse($request, [], "Nova senha criada com sucesso!");
    }

}
