<?php

/*********************************************
* #### Imap_parser.php v0.0.1 ####
* IMAP mailbox parser using PHP.
* Return Array or JSON.
* Coded by @bachors 2016.
* https://github.com/bachors/Imap_parser.php
* Updates will be posted to this site.
- data:
	- email:
		- hostname
		- username
		- password
	- pagination:
		- sort
		- limit
		- offset

- result:
	- status
	- email
	- count
	- inbox:
		- id
		- subject
		- from
		- email
		- date
		- message
		- image
	- pagination
		- sort
		- limit
		- offset
			- back
			- next
*********************************************/

class Imap_parser {

	private bool $toUtf8 = false;
	private ?int $encoding;

	public function __construct(?bool $toUtf8) {
        $this->toUtf8 = $toUtf8 ?? $this->toUtf8;
	}

	public function inbox($data, $toUtf8 = false) {
		$result = array();

		$imap = imap_open($data['email']['hostname'], $data['email']['username'], $data['email']['password']) or die ('Cannot connect to yourdomain.com: ' . imap_last_error());

		if ($imap) {

			$result['status'] = 'success';
			$result['email']  = $data['email']['username'];

			$read = imap_search($imap, 'ALL');

			if ($data['pagination']['sort'] == 'DESC') {
				rsort($read);
			}

			$num = count($read);

			$result['count'] = $num;

			$stop = $data['pagination']['limit'] + $data['pagination']['offset'];

			if ($stop > $num){
				$stop = $num;
			}

			for ($i = $data['pagination']['offset']; $i < $stop; $i++) {
				$overview   = imap_fetch_overview($imap, $read[$i], 0);
				$message    = imap_body($imap, $read[$i], 0);
				$header     = imap_headerinfo($imap, $read[$i], 0);
				$mail       = $header->from[0]->mailbox . '@' . $header->from[0]->host;
				$image = '';

				$message = preg_replace('/--(.*)/i', '', $message);
				$message = preg_replace('/X\-(.*)/i', '', $message);
				$message = preg_replace('/Content\-ID\:/i', '', $message);

				$msg = '';

				$structure = imap_fetchstructure($imap, $read[$i]);
				$encoding = $structure->encoding;

                if (isset($structure->parts) && is_array($structure->parts) && isset($structure->parts[1])) {
                    $part = $structure->parts[1];
                    $structure = imap_fetchbody($imap, $read[$i], 2);
					$this->encoding = $part->encoding;
					$this->toUtf8($structure);
                }

				$this->encoding = $encoding;

				if (preg_match('/Content-Type/', $message)) {
					$message = strip_tags($message);
					$content = explode('Content-Type: ', $message);
					foreach ($content as $c) {
						if (preg_match('/base64/', $c)) {
							$b64 = explode('base64', $c);
							if (preg_match('/==/', $b64[1])) {
								$str = explode('==', $b64[1]);
								$dec = $str[0];
							} else {
								$dec = $b64[1];
							}
							if (preg_match('/image\/(.*)\;/', $c, $mime)) {
								$image = 'data:image/' . $mime[1] . ';base64,' . trim($dec);
							}
						} else {
							if (!empty($c)) {
								$msg = $c;
							}
						}
					}
				} else {
					$msg = $message;
				}

				$msg = preg_replace('/text\/(.*)UTF\-8/', '', $msg);
				$msg = preg_replace('/text\/(.*)\;/', '', $msg);
				$msg = preg_replace('/charset\=(.*)\"/', '', $msg);
				$msg = preg_replace('/Content\-Transfer\-Encoding\:(.*)/i', '', $msg);
				$msg = trim($msg);

				$result['inbox'][] = array(
					'id' => $read[$i],
					'subject' => strip_tags($overview[0]->subject),
					'from' => $overview[0]->from,
					'email' => $mail,
					'date' => $overview[0]->date,
					'structure' => $structure,
					'message' => $this->toUtf8(trim($msg)),
					'image' => $image
				);

				$result['pagination'] = array(
					'sort' => $data['pagination']['sort'],
					'limit' => $data['pagination']['limit'],
					'offset' => array(
						'back' => ($data['pagination']['offset'] == 0 ? null : $data['pagination']['offset'] - $data['pagination']['limit']),
						'next' => ($data['pagination']['offset'] < $num ? $data['pagination']['offset'] + $data['pagination']['limit'] : null)
					)
				);
			}

			imap_close($imap);
		} else {
			$result['status'] = 'error';
		}

		return $result;
	}

	private function toUtf8(string $data): string {
        if ($this->encoding == 3) {
            $data = imap_base64($data);
        } else if($this->encoding == 1) {
            $data = imap_8bit($data);
        } else {
            $data = imap_qprint($data);
        }
        return $this->toUtf8 ? utf8_decode(imap_utf8($data)) : $data;
    }
}
