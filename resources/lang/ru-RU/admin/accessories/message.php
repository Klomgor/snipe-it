<?php

return array(

    'does_not_exist' => 'Аксессуар [:id] не существует.',
    'not_found' => 'Этот аксессуар не найден.',
    'assoc_users'	 => 'Этот аксессуар выдан пользователям в количестве :count. Сделайте возврат аксессуара и попробуйте снова. ',

    'create' => array(
        'error'   => 'Аксессуар не был создан, попробуйте снова.',
        'success' => 'Аксессуар создан.'
    ),

    'update' => array(
        'error'   => 'Аксессуар не обновлён, попробуйте снова',
        'success' => 'Аксессуар обновлён.'
    ),

    'delete' => array(
        'confirm'   => 'Вы уверены, что хотите удалить этот аксессуар?',
        'error'   => 'Возникла проблема при удалении аксессуара. Пожалуйста, попробуйте снова.',
        'success' => 'Аксессуар удалён.'
    ),

     'checkout' => array(
        'error'   		=> 'Ошибка при выдаче аксессуара, попробуйте снова',
        'success' 		=> 'Аксессуар выдан.',
        'unavailable'   => 'Нет доступных аксессуаров для выдачи. Проверьте их количество',
        'user_does_not_exist' => 'Неверный пользователь. Попробуйте снова.',
         'checkout_qty' => array(
            'lte'  => 'Вы пытаетесь выдать :checkout_qty аксессуаров, но в настоящее время доступен только один аксессуар данного типа. Пожалуйста, измените количество для выдачи или доступный остаток и попробуйте снова.|Вы пытаетесь выдать :checkout_qty аксессуаров, но доступно всего :number_currently_remaining. Пожалуйста, измените количество для выдачи или доступный остаток этого аксессуара и попробуйте снова.',
            ),
           
    ),

    'checkin' => array(
        'error'   		=> 'Ошибка при возврате аксессуара, попробуйте снова',
        'success' 		=> 'Аксессуар возвращён.',
        'user_does_not_exist' => 'Неверный пользователь. Попробуйте снова.'
    )


);
