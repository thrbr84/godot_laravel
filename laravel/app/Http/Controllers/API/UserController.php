<?php
/**
 * Controller UserController
 *
 *
 * @author  Thiago Bruno <thiago.bruno@birdy.studio>
 */

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

use App\User;
use App\UserSave;
use App\Http\Resources\UserResource;

/**
 *     @OA\Info(
 *         title="Godot + Laravel",
 *         description="Sample Rest API for use with Godot Engine (https://godotengine.org)",
 *         version="1.0",
 *         @OA\Contact(
 *             email="thiago.bruno@birdy.studio"
 *         ),
 *         @OA\License(
 *             name="Apache 2.0",
 *             url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *         ),
 *     ),

 *     @OA\Tag(
 *         name="User",
 *         description="Routes with user functionality"
 *     ),
 *     @OA\ExternalDocumentation(
 *         description="To see the explanation of the code go to: https://youtube.com.br/thiagobruno",
 *         url="https://youtube.com.br/thiagobruno"
 *     ),
 *
*/

/**
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email/codename and password to get the authentication token",
 *     name="Token Based",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="apiAuth",
 * )
 */

class UserController extends BaseController
{

    /**
     * @OA\Get(
     *     tags={"User"},
     *     summary="Returns a user",
     *     description="Returns the authenticated user",
     *     path="/api/user",
     *     security={{"apiAuth":{}}},


     *     @OA\Response(response="200", description="", @OA\JsonContent()),
     * ),
     *
    */
    public function index(Request $request)
    {
        //
        $user = new UserResource($this->auth);
        return $this->sendResponse($request, $user, __("Listing current user"));
    }

    /**
     * @OA\Put(
     *     tags={"User"},
     *     summary="User Save",
     *     description="Saves the user's save data",
     *     path="/api/user",
     *     security={{"apiAuth":{}}},
     *
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="save_data", type="object")
     *          )
     *     ),

     *     @OA\Response(response="200", description="", @OA\JsonContent()),
     * ),
     *
    */
    public function update(Request $request)
    {

        // Get the authenticated user
        $user_data = User::where("id", $this->auth->id)->first();

        // Update user save data
        if (array_key_exists('save_data', $request->input()))
        {
            // Get first register or create that if not exists
            $userSave = UserSave::firstOrNew([
                'user_id' => $this->auth->id,
            ]);

            // Set the value by request input
            $userSave->value = $request->input('save_data');
            $userSave->save();
        }

        // return user info with save data
        $user = new UserResource($user_data);
        return $this->sendResponse($request, $user, __("User updated successfully"));
    }

    /**
     * @OA\Delete(
     *     tags={"User"},
     *     summary="Removes user account",
     *     description="Permanently deletes the user and save",
     *     path="/api/user",
     *     security={{"apiAuth":{}}},


     *     @OA\Response(response="200", description="", @OA\JsonContent()),
     * ),
     *
    */
    public function destroy(Request $request)
    {
        $user = User::where("id", $this->auth->id)->first();
        $userSaveData = UserSave::where("user_id", $this->auth->id);

        // revoke and remove token info
        $userTokens = $user->tokens;
        foreach($userTokens as $token) {
            $token->revoke();
            $token->delete();
        }

        // remove save data
        $userSaveData->forceDelete();

        // remove user account
        $user->forceDelete();

        return $this->sendResponse($request, [], __("User account removed!"));
    }

    /**
     * @OA\Get(
     *     tags={"User"},
     *     summary="Disconnects the user's account",
     *     description="Logs the user out",
     *     path="/api/user/logout",
     *     security={{"apiAuth":{}}},


     *     @OA\Response(response="200", description="", @OA\JsonContent()),
     * ),
     *
    */
    public function logout(Request $request)
    {
        // Revoke tokens
        $request->user()->token()->revoke();
        $request->user()->token()->delete();

        return $this->sendResponse($request, [], __("User successfully logged out!"));
    }
}
