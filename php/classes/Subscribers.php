<?php

namespace Grav\Theme\tapmorph;

/**
 * this class is used manage the subscribers
 * @final NOT
 * @since 2021.01 PM (27.02.2021) standalone
 * @since PM (11.03.2022) abstract
 */
abstract class Subscribers
{
	const
		key = 'subscribers', sep = '|#|', name = 'name', email = 'email',
		emails = self::email . 's',
		admin = 'admin', success = 'success', exists = 'exists', error = 'error', alias = 'alias',
		errors = self::error . 's', aliases = self::alias . 'es', registration = 'registration';
	private const
		json = '.json',
		regexEmail = "/^[\\w.%+-]+@[\\w.-]+\.[A-Za-z]{2,}$/" //email regex
	;
	protected $lang, $langDef, $auth, $title, $dir, $dirTheme, $url, $recipients, $accounts, $now;


	/**
	 * this function is used to process the registration of the email adresses entered in the registration form
	 * @param string &$response the response key; `self::exists` | `self::success` | `self::error`
	 * @param string &$error the error key
	 * @param string $email the email address to register
	 * @param string $code the authentication code to place in the opt-in url
	 * 										 this parameter should be ignored if the url used is that of a third party
	 */
	protected abstract function register_(string &$response, string &$error, string $email, string $code);

	/**
	 * this function is used to process the opt-in once the subscriber will have confirmed the registration request by clicking on the link sent to him
	 * @param string &$error the error key
	 * @param string $alias used to recover the information about the subscriber
	 */
	protected abstract function optIn_(string &$error, string $alias);

	/**
	 * this function is used to process the deletion of the email address
	 * @param string $email the email address to delete
	 * @param string $flag the deletion flag
	 */
	protected abstract function delete_(string $email, string $flag);

	/**
	 * this function is used to process the deletion of the email addresses
	 * @param string &$response the response key; `self::success` | __ERROR_MESSAGE__
	 * @param array $emails the of emails to delete
	 * @param callable $text the callable used to get the translated text based on the given key
	 */
	protected abstract function deleteBulk_(string &$response, array $emails, callable $text);

	/**
	 * this function is used to get the current list of addresses
	 * @param string $format the format of the opt-in date
	 * 											 @see https://www.php.net/manual/en/function.date for more info
	 * @return array the list of addresses
	 * 							 format: {{date, email}, ...}
	 */
	protected abstract function addresses_(string $format): array;


	/**
	 * @constructor
	 */
	function __construct(
		string $lang,
		string $langDef,
		string $auth,
		string $title,
		string $dir,
		string $dirTheme,
		string $url,
		array $recipients,
		$accounts
	) {
		$this->lang = $lang;
		$this->langDef = $langDef;
		$this->auth = $auth;
		$this->title = $title;
		$this->dir = $dir;
		$this->dirTheme = $dirTheme;
		$this->url = $url;
		$this->recipients = $recipients;
		$this->accounts = $accounts;
		$this->now = date("Y-m-d H:i:s");
	}

	/**
	 * this function is used to check the validity of the given email address
	 * @version 2020.7 @since PM (07.02.2020) 
	 * @param string $email s.e.
	 * @return TRUE if the value entered is that of an actual email address otherwise FALSE
	 */
	static function isEmail(string $email): bool
	{
		return preg_match(self::regexEmail, $email);
	}

	/**
	 * this function is used to start the registration process of the email adresses entered in the registration form
	 * #die()
	 * @version 2020.7 @since PM (06-07.02.2020) standalone
	 * @version 2020.8 @since PM (08.02.2020) object oriented
	 * @version 2020.14 @since PM (01.03.2020) $status has been added
	 * @param bool $status TRUE if the registration module if available
	 * @param string $code the authentication code
	 */
	final function register(bool $status, string $code)
	{
		$response = self::error;
		$error = '';

		if ($status) {
			if (
				isset($_POST[self::name]) && $_POST[self::name] === "" && //name is the honeypot
				self::isEmail($email = $_POST[self::email]) //isset check done in the main .php script
			) $this->register_($response, $error, $email, $code);
			else $error = "email-match-failed [email:$email]";
		} else $error = 'inactive';

		if ($error) $this->errors('REGISTER', "$email $error");
		die($response);
	}

	/**
	 * this function is used to start opt-in process once the subscriber will have confirmed the registration request by clicking on the link sent to him
	 * #redirect()
	 * @version 2020.7 @since PM (06-07.02.2020) standalone
	 * @version 2020.8 @since PM (08.02.2020) object oriented
	 * @version 2020.14 @since PM (01.03.2020) $status has been added
	 * @param bool $status TRUE if the registration module if available
	 */
	final function optIn(bool $status)
	{
		$error = '';

		if ($status) {
			//get the opt-in alias
			if (isset($_GET['data']) && $alias = Utils::clean($_GET['data'])) $this->optIn_($error, $alias);
			else $error = 'alias-empty';
		} else $error = 'inactive';

		$this->errors('OPT-IN', $error);
		$this->redirect(self::error);
	}

	/**
	 * this function is used to start deletion process of the email address
	 * @version 2020.8 @since PM (08.02.2020)
	 * @example http://tapmorph.grav.meppe/en?tapmorph_auth_code=a@a.com&delete=1
	 * @param string $email the email address to delete
	 */
	final function delete(string $email)
	{
		// the status check is missing here on purpose
		// $flag = 'deletion';
		$this->delete_($email, 'deletion');
	}

	/**
	 * this function is used to start the deletion process of the email addresses
	 * #die()
	 * @version 2021.01 @since PM (01.03.2021)
	 * @param callable $text
	 */
	final function deleteBulk(callable $text)
	{
		$response = self::success;
		try { //in case of the unthinkable
			if ($emails = Utils::str2Ar($_POST[self::emails])) //isset check done in the main .php script
				$this->deleteBulk_($response, $emails, $text);
		} catch (\Exception $e) {
			$response = $e->getMessage();
		}

		die($response);
	}

	/**
	 * this function is used to start the listing process of the subscribers currently stored
	 * @param callable $text the callable used to get the translated text based on the given key
	 * @param string $format the format of the opt-in date
	 * 											 @see https://www.php.net/manual/en/function.date for more info
	 * @param callable $processTemplate the callable used to process the adequate twig template
	 * @param string $processTemplate.$suffix the template suffix
	 * @param string $processTemplate.$config the template configuration
	 * @return string the HTML snippet representing the table containing the subscribers
	 */
	final function addresses(callable $text, string $format, callable $processTemplate): string
	{
		$notification = 'notification';
		$idAuth = self::key . "_authenticator";

		if ($this->recipients) {
			// DO NOT MERGE
			if (isset($_GET[self::registration])) {
				$flag = in_array($temp = $_GET[self::registration], [self::success, self::exists]) ? $temp : self::error;
				return $processTemplate(
					$notification,
					compact('idAuth', 'notification', 'flag') + [
						'key' => 'LABELS_TEXTS_RESPONSE_' . strtoupper($flag),
					]
				);
			} else {
				$code = Utils::authCode($this->url);
				$error = $username = $password = '';
				$data = 'data';

				if (isset($_POST[$this->auth]) && $_POST[$this->auth] == $code) {
					$error = $text('SUBSCRIBERS_ERROR_LOGIN');
					if (
						isset($_POST[$data]) &&
						($info = $_POST[$data]) &&
						isset($info['username']) &&
						($username = $info['username']) &&
						isset($info['password']) &&
						($password = $info['password'])
					) {
						/**
						 * @source system\src\Grav\Common\User\Interfaces\UserCollectionInterface.php
						 * @source system\src\Grav\Common\User\Traits\UserTrait.php
						 * @source system\src\Grav\Common\User\Authentication.php
						 */
						$user = $this->accounts->find($username); //works for usernames & emails
						//$user = $this->accounts->load($username); //works for usernames only
						if ($user->exists() && password_verify($password, $user->hashed_password)) { //TRUE if the validation was successful
							foreach ($this->recipients as $recipient) {
								list($recipient) = explode(self::sep, $recipient['recipient']); //email address, username & language used by the admin
								if ($recipient === $user->email) { //TRUE if the user is eligible to receive registration notifications
									//DO NOT MERGE
									return ($addresses = $this->addresses_($format)) ?
										$processTemplate(
											'list',
											[
												'addresses' => $addresses,
												'title' => urlencode($this->title),
												'prefix' => self::key,
												'remove' => Utils::ar2Str([
													$text('SUBSCRIBERS_REMOVE_CONFIRM'),
													$text('SUBSCRIBERS_REMOVE_SUCCESS'),
												])
											]
										) : $processTemplate(
											$notification,
											compact('idAuth', 'notification') + ['key' => 'SUBSCRIBERS_ERROR_NONE']
										);
								}
							}

							//this block is only reached if the email check failed
							$username = $password = '';
							$error = $text('SUBSCRIBERS_ERROR_ACCESS_RIGHTS');
						}
					}
				}

				//this block is only reached if the login process failed
				return $processTemplate(
					'login',
					compact('idAuth', 'notification', 'data', 'username', 'password', 'error', 'code') + [
						'auth' => $this->auth,
					]
				);
			}
		} else return $processTemplate(
			$notification,
			compact('idAuth', 'notification') + ['key' => 'SUBSCRIBERS_ERROR_RECIPIENT']
		);
	}


	/**
	 * this function is used to redirect the client to a different page
	 * #exit
	 * @param string $target the registration
	 */
	protected final function redirect(string $target)
	{
		header("Location: $this->url?" . self::registration . "=$target");
		exit;
	}

	/**
	 * this function is used to notify all the admins
	 * @version 2020.7 @since PM (08.02.2020) standalone
	 * @version 2020.9 @since 09.02.2020 $prefix was added
	 * @param string $mode registration|deletion|deletion_error
	 * @param string $email the subscriber email address
	 * @param string $prefix the error prefix
	 */
	protected final function notifyAdmins(string $mode, string $subscriber, string $prefix)
	{
		if ($this->recipients && ($texts = $this->readTexts())) { //try to notify & forget
			$error = '';

			foreach ($this->recipients as $recipient) {
				list($admin, $lang, $username) = explode(self::sep, $recipient['recipient']); //email address, language, username used by the admin
				if (self::isEmail($admin) && $this->accounts->find($admin, ['email'])->exists()) { //TRUE if the admin email is valid & that of an actual user/account in GRAV
					list(, $er) = $this->notify( //notify the system admin about the new email address
						$admin,
						self::admin . "_$mode",
						$texts[$lang ?: $this->lang] ?: $texts[$this->langDef],
						['__USERNAME__', '__EMAIL_ADDRESS__'],
						[$username, $subscriber]
					);
					if ($er) $error .= "$er\n";
				}
			}

			if ($error) $this->errors($prefix, $error);
		}
	}

	/**
	 * this function is used to send a mail notification
	 * @see https://github.com/getgrav/grav-plugin-email/blob/develop/README.md#programmatically-send-emails
	 * @version 2020.7 @since PM (08.02.2020) standalone
	 * @param string $email the email address
	 * @param string $mode admin_[registration|deletion|deletion_error]|subscriber
	 * @param array $text the text to send
	 * @param array $phs the placeholders
	 * @param array $reps the replacers
	 * @return {TRUE, ''} if everything went as expected, otherwise {FALSE, '...'}
	 */
	protected final function notify(string $email, string $mode, array $text, array $phs, array $reps): array
	{
		try {
			/** @see https://github.com/getgrav/grav-plugin-email/blob/develop/README.md#programmatically-send-emails #alternative */
			$sent = mail( //notify the system user about the new email address
				$email,
				"$this->title: " . $text["subject_$mode"],
				str_replace(
					array_merge($phs, ['__TITLE__']),
					array_merge($reps, [$this->title]),
					implode("<br>", $text["message_$mode"])
				),
				[
					'MIME-Version' => '1.0',
					'Content-type' => 'text/html; charset=UTF-8',
					'From' => "info@" . $_SERVER["SERVER_NAME"],
					'X-Mailer' => 'PHP/' . phpversion()
				]
			);
			return [
				$sent,
				$sent ? '' : "mail-not-sent [$mode:$email]"
			];
		} catch (\Exception $e) {
			return [
				Utils::dev(),
				"mail-exception [$mode:$email], [exception:" . str_replace("\n", "-", $e->getMessage()) . "]"
			];
		}
	}


	/**
	 * this function is used during the development to debug the registration process by logging errors
	 * @version 2020.9 @since 09.02.2020 $prefix has been added
	 * @param string $prefix s.e.
	 * @param string $error the error message to log
	 */
	protected final function errors(string $prefix, string $error)
	{
		if (Utils::dev()) file_put_contents(
			Utils::path($this->dir, self::errors . ".log"),
			"$this->now $prefix\n" . trim($error) . "\n\n",
			FILE_APPEND | LOCK_EX
		);
	}


	/**
	 * this function is used to read stored JSON from a given file
	 * @param string $name the filename **without** the .json format
	 * @return array
	 */
	protected final function read(string $name): array
	{
		return Utils::str2Ar(@file_get_contents(Utils::path($this->dir, $name . self::json)));
	}

	/**
	 * this function is used to read the admin stored JSON texts
	 */
	protected final function readTexts(): array
	{
		return array_merge_recursive(
			Utils::str2Ar(@file_get_contents(Utils::path($this->dirTheme, 'languages', self::admin . self::json))), //standard
			$this->read('languages') //custom
		);
	}

	/**
	 * this function is used to store arrays in a given file
	 * @param string $name the filename **without** the .json format
	 * @param array $data
	 * @return int|FALSE
	 */
	protected final function write(string $name, array $data)/*:int|FALSE*/
	{
		return file_put_contents(
			Utils::path($this->dir, $name . self::json),
			preg_replace("/ {4}/", "\t", Utils::ar2Str($data, JSON_PRETTY_PRINT)),
			LOCK_EX
		);
	}
}
