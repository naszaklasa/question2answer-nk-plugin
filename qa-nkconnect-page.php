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

class qa_nkconnect_page
{
  const FIELD_SECRET = 'nkconnect_field_secret';
  const FIELD_KEY = 'nkconnect_field_key';

  public function admin_form()
  {
    $saved = false;
    
    if (qa_clicked('nkconnect_save_button')) {
      qa_opt(Q2ANKConnect::NKCONNECT_SECRET, qa_post_text(self::FIELD_SECRET));
      qa_opt(Q2ANKConnect::NKCONNECT_KEY, qa_post_text(self::FIELD_KEY));
      $saved = true;
    }
    
    return array(
        'ok' => $saved ? 'ustawienia nkConnect zapisane' : null, 
        
        'fields' => array(
            array(
                'label' => 'nkConnect sekret:', 
                'value' => qa_html(qa_opt(Q2ANKConnect::NKCONNECT_SECRET)), 
                'tags' => 'NAME="' . self::FIELD_SECRET . '"'
            ), 
            
            array(
                'label' => 'nkConnect klucz:', 
                'value' => qa_html(qa_opt(Q2ANKConnect::NKCONNECT_KEY)), 
                'tags' => 'NAME="' . self::FIELD_KEY . '"'
            )
        ), 
        
        'buttons' => array(
            array(
                'label' => 'Zapisz zmiany', 
                'tags' => 'NAME="nkconnect_save_button"'
            )
        )
    );
  }

}

?>