<?php

namespace App\Controllers;

use App\Models\User;
use Sirius\Validation\Validator;
use App\Log;

class AuthController extends BaseController {

	public function getLogin()
	{
		return $this->render('login.twig');
	}

	public function postLogin()
	{
		$validator = new Validator();
		$validator->add('email', 'required');
		$validator->add('email', 'email');
		$validator->add('password', 'required');

		if($validator->validate($_POST)){
			$user = User::where('email', $_POST['email'])->first();
			
			if($user) {
				if(password_verify($_POST['password'], $user->password)){

					$_SESSION['userId'] = $user->id;
					Log::logInfo('Login userId:' . $user->id);
					header('Location:' . BASE_URL . 'admin');

				}
			}
			$validator->addMessage('email', 'Usersname and/or password does not match');

		}

		$errors = $validator->getMessages();

		return $this->render('login.twig', [
			'errors' => $errors,
		]);
	}

	public function getLogout()
	{
		Log::logError('Loguot userId:'. $_SESSION['userId']);
		unset($_SESSION['userId']);

		header('Location:' . BASE_URL . 'auth/login');
	}
}