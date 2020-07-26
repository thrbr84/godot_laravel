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
 *         description="API Rest de exemplo para ser utilizada com Godot Engine (https://godotengine.org)",
 *         version="1.0",
 *         @OA\Contact(

 *             email="thiago.bruno@birdy.studio"
 *         ),
 *         @OA\License(
 *             name="Apache 2.0",
 *             url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *         )
 *     ),
 *     @OA\Tag(
 *         name="User",
 *         description="Rotas com funcionalidades do usuário"
 *     ),
 *     @OA\ExternalDocumentation(
 *         description="Para ver a explicação sobre o código acesse: https://youtube.com.br/thiagobruno",
 *         url="https://youtube.com.br/thiagobruno"
 *     )
 *
*/

/**
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="Token based Based",
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
     *     summary="Retorna o usuário autenticado",
     *     description="Retorna um usuário",
     *     path="/api/user",
     *     security={{"apiAuth":{}}},


     *     @OA\Response(response="200", description="Listando o usuário", @OA\JsonContent()),
     * ),
     *
    */
    public function index(Request $request)
    {
        //
        $user = new UserResource($this->auth);
        return $this->sendResponse($request, $user, "Listando usuário atual");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @OA\Put(
     *     tags={"User"},
     *     summary="Save do usuário",
     *     description="Grava os dados de save do usuário",
     *     path="/api/user",
     *     security={{"apiAuth":{}}},
     *
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="save_data", type="object")
     *          )
     *     ),

     *     @OA\Response(response="200", description="Usuário salvo com sucesso", @OA\JsonContent()),
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
        return $this->sendResponse($request, $user, "Usuário atualizado com sucesso");
    }

    /**
     * @OA\Delete(
     *     tags={"User"},
     *     summary="Remove a conta do usuário",
     *     description="Exclui o usuário e o save de forma permanente",
     *     path="/api/user",
     *     security={{"apiAuth":{}}},


     *     @OA\Response(response="200", description="Usuário excluído com sucesso", @OA\JsonContent()),
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
     *     summary="Desconecta a conta do usuário",
     *     description="Efetua o logout do usuário",
     *     path="/api/user/logout",
     *     security={{"apiAuth":{}}},


     *     @OA\Response(response="200", description="Usuário desconectado com sucesso", @OA\JsonContent()),
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
