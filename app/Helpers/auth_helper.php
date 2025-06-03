<?php

if ( ! function_exists('restore_account')) 
{
	function restore_account() { 
		$session = session();

		$account = $session->get(AUTH_BASE_ACCOUNT);

		if (!empty($account)) {
			if (is_array($account)) {
				set(end($account));
				$session->set(AUTH_BASE_ACCOUNT, $account[count($account) - 2]);
			} else {
				set($account);
				$session->remove(AUTH_BASE_ACCOUNT);
			}
		}
	} 
}

if ( ! function_exists('set_restore_account')) 
{
	function set_restore_account($account) { 
		$session = session();

		$prev_account = $session->get(AUTH_BASE_ACCOUNT);
		if (empty($account)) {
			$session->set(AUTH_BASE_ACCOUNT, $prev_account);
		} else {
			$session->set(AUTH_BASE_ACCOUNT, [$prev_account,$account]);
		}
	} 
}

if ( ! function_exists('is_restore_account')) 
{
	function is_restore_account() { 
		$session = session();
		return !empty($session->get(AUTH_BASE_ACCOUNT));
	} 
}

if ( ! function_exists('get_restore_account')) 
{
	function get_restore_account() { 
		$session = session();
		return $session->get(AUTH_BASE_ACCOUNT);
	} 
}

if ( ! function_exists('auth_checker_init')) 
{
	function auth_checker_init() { 
		$session = session();
		
		$userdata = $session->get(AUTH_SESS_NAME);

		if (!empty($userdata)) {
			if ($userdata->role == 'operator') redirect('public/dashboard');
			redirect('dashboard');
		}
	} 
}

if ( ! function_exists('old_auth_checker')) 
{
	function old_auth_checker($roles) { 
		$session = session();

		$userdata = $session->get(AUTH_SESS_NAME);

		if (empty($userdata)) {
			// Save current url
			$session->set(AUTH_VISIT_SESS_NAME, uri_string());
			redirect('auth');
		} else if (!in_array($userdata->role, explode(',', $roles))) {
			redirect('auth/error/403');
		}
	} 
}

if ( ! function_exists('auth_checker')) 
{
	function auth_checker($roles = NULL) { 
		$session = session();

		$userdata = $session->get(AUTH_SESS_NAME);

		if (empty($userdata)) {
			return false;
		} else if (!empty($roles) && !in_array($userdata->role, explode(',', $roles))) {
			return false;
		}
		return true;
	} 
}

if ( ! function_exists('is_users')) 
{
	function is_users($roles) { 
		$session = session();

		$userdata = $session->get(AUTH_SESS_NAME);

		if (empty($userdata)) {
			// Save current url
			$session->set(AUTH_VISIT_SESS_NAME, uri_string());
			redirect('auth');
		} else if (!in_array($userdata->role, explode(',', $roles))) {
			return false;
		}
		return true;
	} 
}

if ( ! function_exists('set_userdata')) 
{
	function set_userdata($data) { 
		$session = session();
		$session->set(AUTH_SESS_NAME, $data);
	} 
}

if ( ! function_exists('userdata')) 
{
	function userdata() { 
		$session = session();

		$userdata = $session->get(AUTH_SESS_NAME);

		if (empty($userdata)) return NULL;

		return $userdata;
	} 
}

if ( ! function_exists('get_visited_url')) 
{
	function get_visited_url() { 
		$session = session();

		$visited_url = $session->get(AUTH_VISIT_SESS_NAME);

		if (empty($visited_url)) return NULL;

		return $visited_url;
	} 
}

if ( ! function_exists('clear_userdata')) 
{
	function clear_userdata() { 
		$session = session();
		$session->remove(AUTH_SESS_NAME);
		$session->remove(AUTH_VISIT_SESS_NAME);
	} 
}
