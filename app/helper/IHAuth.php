<?php

interface IHAuth{

	public function IsLogged();

	public function IsSuper();

	public function LoginUser();

	public function LoginSuper();

	public function LogoutUser();

	public function LogoutSuper();

	public function ResetPwd();

	public function RegisterUser();

	public function RegisterSuper();

}