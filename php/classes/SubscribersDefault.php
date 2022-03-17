<?php

namespace Grav\Theme\tapmorph;

use DateTime;

/**
 * this class is the default way to manage the subscribers
 * @final 
 * @since PM (11.03.2022) standalone
 */
final class SubscribersDefault extends Subscribers
{

	/**
	 * @override
	 * @since PM (10.03.2022) standalone
	 */
	protected function register_(string &$response, string &$error, string $email, string $code)
	{
		$subscribers = $this->read(self::key);
		if (isset($subscribers[$email]) && $subscribers[$email]) $response = self::exists;
		else {
			$aliases = $this->read(self::aliases);
			do $alias = uniqid($this->now . "_", true);
			while (isset($aliases[$alias]) && $aliases[$alias]); //making sure the alias is really unique

			$aliases[$alias] = base64_encode(implode(self::sep, [$this->now, $email])); //request date & email address
			if ($this->write(self::aliases, $aliases)) { //TRUE if the data were successfully stored
				$url = "$this->url?" . $this->auth . "=" . urlencode($code) . "&data=" . urlencode($alias);
				if (Utils::dev()) file_put_contents(
					Utils::path($this->dir, 'urls.log.md'),
					"[$alias]($url)<br>\n",
					FILE_APPEND | LOCK_EX
				);
				// ...
				if ($texts = $this->readTexts()) {
					list($sent, $error) = $this->notify( //send the confirmation mail to the subscriber
						$email,
						'subscriber',
						$texts[$this->lang] ?: $texts[$this->langDef],
						['__URL_PAGE__', '__URL_CONFIRMATION__', '__TITLE__'],
						[$this->url, $url, $this->title]
					);
					if ($sent) $response = self::success;
				} else $error = "texts-missing";
			} else $error = "alias-storage-failed";
		}
	}

	/**
	 * @override
	 * #redirect()
	 * @since PM (10.03.2022) standalone
	 */
	protected function optIn_(string &$error, string $alias)
	{
		$aliases = $this->read(self::aliases);
		$subscribers = $this->read(self::key);

		//check the availability of the alias
		if ($data = $aliases[$alias]) $found = true;
		else {
			foreach ($subscribers as $email) if ($email[self::alias] == $alias) { //TRUE if the alias has already been used in the past
				$this->redirect(self::exists); //the address exists already
				break; //this line will never be reached, but aw well :-)
			}
			$found = false; //the line will only be reached if the alias has never been used before
		}
		//DO NOT MERGE
		if ($found) { //TRUE if the alias has been found in the list of aliases
			list($date, $email) = explode(self::sep, base64_decode($data));

			$d = "\\d{2}";
			if (preg_match("@$d$d-$d-$d $d:$d:$d@", $date) && self::isEmail($email)) {
				//DO NOT MERGE
				if (isset($subscribers[$email]) && $subscribers[$email]) {
					unset($aliases[$alias]); //remove the alias
					$this->write(self::aliases, $aliases);

					$this->redirect(self::exists); //the address exists already
				} else { //add the address to list
					$subscribers[$email] = ['request' => $date, 'opt_in' => $this->now, self::alias => $alias]; //new entry
					if ($this->write(self::key, $subscribers)) { //TRUE if the new email was successfully stored
						$this->notifyAdmins(self::registration, $email, 'OPT-IN');

						unset($aliases[$alias]); //remove the alias
						$this->write(self::aliases, $aliases);
						$this->redirect(self::success);
					} else $error = "email-storage-failed [email:$email]";
				} //endelse
			} else $error = "data-invalid [date:$date], [email:$email]";
		} else $error = "alias-invalid [alias:$alias]";
	}

	/**
	 * @override
	 * #redirect()
	 * @since PM (10.03.2022) standalone
	 */
	protected function delete_(string $email, string $flag)
	{
		$subscribers = $this->read(self::key); //load the list of subscribers
		if ($subscribers[$email]) { //TRUE if email has been found
			unset($subscribers[$email]); //remove the email address from the list

			$prefix = "DELETE";
			if ($this->write(self::key, $subscribers)) { //update & close the list
				$this->notifyAdmins($flag, $email, $prefix);
				$this->redirect("$flag-" . self::success);
			} else {
				$this->errors($prefix, "email-storage-failed [email:$email]");

				$this->notifyAdmins("${flag}_" . self::error, $email, $prefix);
				$this->redirect("$flag-" . self::error);
			}
		} else $this->redirect("$flag-not-found");
	}

	/**
	 * @override
	 * @since PM (10.03.2022) standalone
	 */
	protected function deleteBulk_(string &$response, array $emails, callable $text)
	{
		$subscribers = $this->read(self::key); //get the emails addresses from the repository
		foreach ($emails as $email) if ($subscribers[$email]) unset($subscribers[$email]); //remove the emails

		$emails2 = "<br>\n-" . implode("<br>\n-", $emails);
		// $emails2 = implode("<br>\n-", ['', ...$emails]);
		$prefix = "DELETE";
		if ($this->write(self::key, $subscribers)) //store the updated subscribers list
			$this->notifyAdmins('deletion', $emails2, $prefix);
		else {
			$response = $text('SUBSCRIBERS_ERROR_REMOVE');
			$this->errors($prefix, "email-storage-failed [email2:$emails2]");
			$this->notifyAdmins('deletion_' . self::error, $emails2, $prefix);
		}
	}

	/**
	 * @override
	 * @since PM (10.03.2022) standalone
	 */
	protected function addresses_(string $format): array
	{
		if ($subscribers = $this->read(self::key)) { //get the subscribers from the repository
			ksort($subscribers); //sort them alphabetically
			$addresses = [];
			foreach ($subscribers as $email => $info) if (self::isEmail($email)) {
				//@see https://en.wikipedia.org/wiki/Mailto
				try {
					$date = (new DateTime($info['opt_in']))->format($format);
				} catch (\Exception $e) {
					$date = '...';
				}
				$addresses[] = [
					'date' => $date,
					'email' => $email,
				];
			}

			return $addresses;
		} else return [];
	}
}
