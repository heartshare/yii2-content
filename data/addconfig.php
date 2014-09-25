<?php
return [
    'articles_pp' => [
        'param' => 'articles_pp',
        'name' => 'Статей на страницу',
        'descr' => '',
        'value' => 10,
        'type' => 'drop',
        'cust' => [5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30],
        'section' => 'Контент'
    ],
    'news_pp' => [
        'param' => 'news_pp',
        'name' => 'Новостей на страницу',
        'descr' => '',
        'value' => 10,
        'type' => 'drop',
        'cust' => [5 => 5, 10 => 10, 15 => 15, 20 => 20, 25 => 25, 30 => 30],
        'section' => 'Контент'
    ],
    'uploadpath' => [
        'param' => 'uploadpath',
        'name' => 'Путь для сохранения изображений',
        'descr' => '',
        'value' => '@frontend/web/uploads',
        'type' => 'string',
        'section' => 'Контент'
    ],
    'use_newscover' => [
        'param' => 'use_newscover',
        'name' => 'Использовать обложки для новостей',
        'descr' => 'Изображения-обложки будут аккуратно отображаться в виджетах и автоматом позиционроваться в начале новости',
        'value' => '1',
        'type' => 'bool',
        'section' => 'Контент'
    ],
    'use_artcover' => [
        'param' => 'use_artcover',
        'name' => 'Использовать обложки для статей',
        'descr' => 'Изображения-обложки будут аккуратно отображаться в виджетах и автоматом позиционроваться в начале статьи',
        'value' => '1',
        'type' => 'bool',
        'section' => 'Контент'
    ],
    'thumb_size' => [
        'param' => 'thumb_size',
        'name' => 'Размер превью статей/новостей',
        'descr' => 'Ширина, высота через запятую',
        'value' => '180,180',
        'type' => 'string',
        'section' => 'Контент'
    ],
    'mid_size' => [
        'param' => 'mid_size',
        'name' => 'Размер фото в статьях/новостях',
        'descr' => '(макс. сторона, в пикс.)',
        'value' => 450,
        'type' => 'string',
        'section' => 'Контент'
    ],
    'big_size' => [
        'param' => 'big_size',
        'name' => 'Размер фото в статьях/новостях при увеличении',
        'descr' => '(макс. сторона, в пикс.)',
        'value' => 1000,
        'type' => 'string',
        'section' => 'Контент'
    ],
    'viewscount' => [
        'param' => 'viewscount',
        'name' => 'Считать просмотры?',
        'descr' => 'Считать просмотры статей\новостей\страниц',
        'value' => 1000,
        'type' => 'bool',
        'section' => 'Контент'
    ],
    'show_contactform' => [
        'param' => 'show_contactform',
        'name' => 'Отображать форму для отправки сообщения',
        'descr' => '',
        'value' => 1,
        'type' => 'bool',
        'section' => 'Страница обратной связи'
    ],
    'contactmail' => [
        'param' => 'contactmail',
        'name' => 'Email на который будут отправляться уведомления',
        'descr' => 'Оставьте пустым чтоб не получать email, сообщения можно просматривать в админке',
        'value' => '',
        'type' => 'text',
        'section' => 'Страница обратной связи'
    ],
    'show_contactinfo' => [
        'param' => 'show_contactinfo',
        'name' => 'Отображать блок с контактной информацией',
        'descr' => '',
        'value' => 1,
        'type' => 'bool',
        'section' => 'Страница обратной связи'
    ],
    'contactinfo' => [
        'param' => 'contactinfo',
        'name' => 'Содержание блока с контактной информацией',
        'descr' => '',
        'value' => '',
        'type' => 'extended',
        'section' => 'Страница обратной связи'
    ],
    'pageLayout' => [
        'param' => 'pageLayout',
        'name' => 'Макет сайта - страницы',
        'descr' => 'Страницы создаваемые в соответсв. разделе + форма обратной связи',
        'value' => 'col2_left',
        'type' => 'drop',
        'cust' => [
            'main' => '1 колонка',
            'col2_left' => '2 колонки, узкая слева',
            'col2_right' => '2 колонки, узкая справа',
            'col3' => '3 колонки'
        ],
        'section' => 'Внешний вид'
    ],
    'newsLayout' => [
        'param' => 'newsLayout',
        'name' => 'Макет сайта - Новости',
        'descr' => '',
        'value' => 'col2_left',
        'type' => 'drop',
        'cust' => [
            'main' => '1 колонка',
            'col2_left' => '2 колонки, узкая слева',
            'col2_right' => '2 колонки, узкая справа',
            'col3' => '3 колонки'
        ],
        'section' => 'Внешний вид'
    ],
    'artLayout' => [
        'param' => 'artLayout',
        'name' => 'Макет сайта - Статьи',
        'descr' => '',
        'value' => 'col2_left',
        'type' => 'drop',
        'cust' => [
            'main' => '1 колонка',
            'col2_left' => '2 колонки, узкая слева',
            'col2_right' => '2 колонки, узкая справа',
            'col3' => '3 колонки'
        ],
        'section' => 'Внешний вид'
    ],

];