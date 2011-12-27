<?php

/**
 * Copyright 2011 Nasza Klasa Spółka z ograniczoną odpowiedzialnością
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author Mariusz Pańszczyk <mariusz.panszczyk@nasza-klasa.pl>
 * @link http://developers.nk.pl
 * 
 */

require_once 'Q2ANKConnect.php';
require_once QA_INCLUDE_DIR . 'qa-db-users.php';
require_once QA_INCLUDE_DIR . 'qa-app-users.php';

class qa_nkconnect
{
  const SOURCE = 'nkconnect';

  function check_login()
  {
    if ( !qa_get_logged_in_userid() > 0) {
      try{
        $auth = Q2ANKConnect::instance()->getAuth();
        $auth->handleCallback();
        if ($auth->authenticated()) {
          $this->join_or_add($auth->user());
          $auth->logout();
        }
      }catch (NKConnectUnusableException $e){
        error_log($e->getMessage());
      }
    }
  }

  function login_html($tourl, $context)
  {
    if ( !Q2ANKConnect::is_ready_data()) {
      return;
    }
    echo Q2ANKConnect::instance()->get_login_button();
  }

  /**
   * 
   * @param NKUser $userData
   */
  private function join_or_add(NKUser $userData)
  {
    $email_users = qa_db_user_find_by_email($userData->email());
    
    if (count($email_users) === 1) {
      $this->join_user_data($email_users[0], $userData);
    }
    qa_log_in_external_user(Q2ANKConnect::LOGIN_SOURCE, $userData->id(), array(
        'email' => $userData->email(), 
        'avatar' => $userData->thumbnailUrl(), 
        'name' => $userData->name(), 
        'confirmed' => true, 
        'handle' => $this->generateUserHandle($userData)
    ));
  }

  /**
   * 
   * @param string $userId
   * @param NKUser $userData
   */
  private function join_user_data($userId, NKUser $userData)
  {
    $user = qa_db_user_login_find(Q2ANKConnect::LOGIN_SOURCE, $userData->id());
    if (count($user) === 0) {
      qa_db_user_login_add($userId, Q2ANKConnect::LOGIN_SOURCE, $userData->id());
    }
  }

  private function generateUserHandle(NKUser $userData)
  {
    $name = preg_replace('/[^a-z0-9.]/i', '', $this->remove_plchars($userData->name()));
    
    $check_name = true;
    
    while ($check_name) {
      $find = qa_db_user_find_by_handle($name);
      if (count($find) > 0) {
        $name .= mt_rand(0, 9);
      } else {
        $check_name = false;
      }
    }
    return $name;
  }

  private function remove_plchars($str)
  {
    return str_replace(
      array('ą', 'Ą', 'ć', 'Ć', 'ę', 'Ę', 'ł', 'Ł', 'ń', 'Ń', 'ó', 'Ó', 'ś', 'Ś', 'ź', 'Ź', 'ż', 'Ż'), 
      array('a', 'A', 'c', 'C', 'e', 'E', 'l', 'L', 'n', 'N', 'o', 'O', 's', 'S', 'z', 'Z', 'z', 'Z'),
    $str);
  }
}

?>