<?php
// localization.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ================= DEFAULT LANGUAGE ================= */
$defaultLang = $_SESSION['custom_lang'] ?? $_COOKIE['custom_lang'] ?? 'en';

/* ================= RTL LANGUAGES ================= */
$rtlLanguages = ['ps','fa','ar'];

/* ================= TRANSLATIONS ================= */
$translations = [

    /* --- Sidebar / Menu --- */

    "welcome" => "ښه راغلاست",
    "login"   => "ننوتل",
   

    'dashboard'             => 'Dashboard',
    'dashboard_ps'          => 'ډشبورډ',

    'expenses_add'          => 'Add Expense',
    'expenses_add_ps'       => 'مصرف اضافه کړئ',

    'reports_expenses'      => 'Expense Reports',
    'reports_expenses_ps'   => 'د مصرف راپورونه',

    'income_add'            => 'Add Income',
    'income_add_ps'         => 'عاید اضافه کړئ',

    'clients_add'           => 'Add Client',
    'clients_add_ps'        => 'مراجع اضافه کړئ',

    'client_payment'        => 'Client Payment',
    'client_payment_ps'     => 'د مراجع تادیه',

    'monthly_report'        => 'Monthly Report',
    'monthly_report_ps'     => 'میاشتنی راپور',

    /* --- Consumer / Client --- */
    'add_consumer'          => 'Add Client',
    'add_consumer_ps'       => 'مراجع اضافه کړئ',
    'edit_consumer'         => 'Edit Client',
    'edit_consumer_ps'      => 'د مراجع سمون',
    'consumer_list'         => 'Clients List',
    'consumer_list_ps'      => 'د مراجعو لیست',

    /* --- Form Fields / Placeholders --- */
    'placeholder_name'      => 'Enter Name',
    'placeholder_name_ps'   => 'نوم ولیکئ',
    'placeholder_phone'     => 'Enter Phone',
    'placeholder_phone_ps'  => 'ټیلیفون ولیکئ',
    'placeholder_email'     => 'Enter Email',
    'placeholder_email_ps'  => 'برېښنالیک ولیکئ',

    /* --- Buttons / Actions --- */
    'save'                  => 'Save',
    'save_ps'               => 'ثبت',
    'update'                => 'Update',
    'update_ps'             => 'سمون',
    'delete'                => 'Delete',
    'delete_ps'             => 'حذف',
    'delete_confirm'        => 'Are you sure you want to delete this item?',
    'delete_confirm_ps'     => 'ایا تاسې ډاډه یاست چې دا حذف کړئ؟',

    /* --- Receipt Form --- */
    'add_receipt'           => 'Add Receipt',
    'add_receipt_ps'        => 'رسید اضافه کړئ',
    'receipt_client'        => 'Client',
    'receipt_client_ps'     => 'مراجع',
    'receipt_amount'        => 'Amount (AFN)',
    'receipt_amount_ps'     => 'مقدار (افغانی)',
    'receipt_date'          => 'Date',
    'receipt_date_ps'       => 'نېټه',
    'receipt_note'          => 'Receipt / Note',
    'receipt_note_ps'       => 'رسید / یادښت',
    'receipt_submit'        => 'Save Receipt',
    'receipt_submit_ps'     => 'رسید ثبت کړئ',

    /* --- Receipts Table --- */
    'receipts_title'        => 'Receipt add',
    'receipts_title_ps'     => 'د رمراجعونو ثبت',
    'receipts_list'         => 'Receipts List',
    'receipts_list_ps'      => 'د رسیدونو لیست',
    'table_number'          => '#',
    'table_number_ps'       => 'شمېره',
    'table_client'          => 'Client',
    'table_client_ps'       => 'مراجع',
    'table_amount'          => 'Amount',
    'table_amount_ps'       => 'مقدار',
    'table_date'            => 'Date',
    'table_date_ps'         => 'نېټه',
    'table_note'            => 'Note',
    'table_note_ps'         => 'یادښت',
    'table_description'     => 'Note',
    'table_description_ps'  => 'یادښت',
    'table_actions'         => 'Actions',
    'table_actions_ps'      => 'عملیات',
     
    /* --- Reports / Monthly Expense --- */
    'monthly_report_title'      => 'Monthly Expense Report',
    'monthly_report_title_ps'   => 'د میاشتنی مصرف راپور',
    'monthly_report_year'       => 'Year',
    'monthly_report_year_ps'    => 'کال',
    'monthly_report_month'      => 'Month',
    'monthly_report_month_ps'   => 'میاشت',
    'monthly_report_show'       => 'Show',
    'monthly_report_show_ps'    => 'ښودل',
    'monthly_report_total'      => 'Total',
    'monthly_report_total_ps'   => 'ټول',
    'monthly_report_no_data'    => 'No expenses found for selected month.',
    'monthly_report_no_data_ps' => 'د ټاکل شوې میاشت لپاره مصرف ونه موندل شو',

    /* --- Monthly Report Form Labels --- */
    'select_client'         => 'Select Client',
    'select_client_ps'      => 'مراجع وټاکئ',
    'client_placeholder'    => 'Select Client',
    'client_placeholder_ps' => 'مراجع وټاکئ',
    'select_month'          => 'Select Month',
    'select_month_ps'       => 'میاشت وټاکئ',
    'show_report'           => 'Show Report',
    'show_report_ps'        => 'راپور وښایاست',
        /* --- Expenses Form --- */
    'expenses_title'        => 'Expenses Management',
    'expenses_title_ps'     => 'د مصرفونو مدیریت',
    'expense_category'      => 'Select Category',
    'expense_category_ps'   => 'کټګوري وټاکئ',
    'expense_amount'        => 'Amount',
    'expense_amount_ps'     => 'مقدار',
    'expense_date'          => 'Expense Date',
    'expense_date_ps'       => 'د مصرف نېټه',
    'expense_description'   => 'Description',
    'expense_description_ps'=> 'تفصیل',
    'expense_submit'        => 'Add Expense',
    'expense_submit_ps'     => 'مصرف ثبت کړئ',
    'expense_list'          => 'Expenses List',
    'expense_list_ps'       => 'د مصرفونو لست',
    'expense_actions'       => 'Actions',
    'expense_actions_ps'    => 'عملیات',
    'edit'                  => 'Edit',
    'edit_ps'               => 'تازه کول',
    'delete'                => 'Delete',
    'delete_ps'             => 'حذف',
    'confirm_delete'        => 'Are you sure?',
    'confirm_delete_ps'     => 'ډاډه یې؟',
    'no_expenses'           => 'No expenses found',
    'no_expenses_ps'        => 'مصارف ونه موندل شول',
        /* --- Income Form --- */
    'income_title'          => 'Income Management',
    'income_title_ps'       => 'د عاید مدیریت',
    'income_date'           => 'Income Date',
    'income_date_ps'        => 'د عاید نېټه',
    'income_category'       => 'Select Category',
    'income_category_ps'    => 'کټګوري وټاکئ',
    'income_amount'         => 'Amount',
    'income_amount_ps'      => 'مقدار',
    'income_description'    => 'Description',
    'income_description_ps' => 'تفصیل',
    'income_submit'         => 'Add Income',
    'income_submit_ps'      => 'عاید ثبت کړئ',
    'income_list'           => 'Income List',
    'income_list_ps'        => 'د عایدونو لست',

    /* --- Common Actions --- */
    'edit'                  => 'Edit',
    'edit_ps'               => 'تازه کول',
    'delete'                => 'Delete',
    'delete_ps'             => 'حذف',
    'confirm_delete'        => 'Are you sure?',
    'confirm_delete_ps'     => 'ډاډه یې؟',
    'no_expenses'           => 'No expenses found',
    'no_expenses_ps'        => 'مصارف ونه موندل شول',
    'no_incomes'            => 'No incomes added yet',
    'no_incomes_ps'         => 'تر دې دمه عاید نه دی اضافه شوی',
    
    /* --- Sidebar / Menu --- */
    'dashboard'             => 'Dashboard',
    'dashboard_ps'          => 'ډشبورډ',

    'clients_add'           => 'Add Client',
    'clients_add_ps'        => 'مراجع اضافه کړئ',

    'client_payment'        => 'Client Payment',
    'client_payment_ps'     => 'د مراجع تادیه',
    
    /* ===== Dashboard ===== */
    'dashboard_title'     => 'Dashboard',
    'dashboard_title_ps'  => 'تشبورد',
    'welcome'             => 'Welcome',
    'welcome_ps'          => 'ښه راغلاست',
    'total_expenses'      => 'Total Expenses',
    'total_expenses_ps'   => 'ټولې مصرفونه',
    'show'                => 'Show',
    'show_ps'             => 'ښودل',
    'latest_expenses'     => 'Latest Expenses',
    'latest_expenses_ps'  => 'وروستي مصرفونه',
    'date'                => 'Date',
    'date_ps'             => 'نېټه',
    'category'            => 'Category',
    'category_ps'         => 'کټګوري',
    'amount'              => 'Amount',
    'amount_ps'           => 'مقدار',
    'description'         => 'Description',
    'description_ps'      => 'تفصیل',
    'actions'             => 'Actions',
    'actions_ps'          => 'عملیات',
    'edit'                => 'Edit',
    'edit_ps'             => 'تازه کول',
    'delete'              => 'Delete',
    'delete_ps'           => 'حذف',
    'confirm_delete'      => 'Are you sure?',
    'confirm_delete_ps'   => 'ایا ډاډه یی؟',
    
      'dashboard_title'        => 'Dashboard',
    'dashboard_title_ps'     => 'تشبورد',

    'welcome'                => 'Welcome to your Dashboard!',
    'welcome_ps'             => 'ستاسو تشبورد ته ښه راغلاست!',

    'total_expenses'         => 'Total Expenses',
    'total_expenses_ps'      => 'ټول مصرفونه',

    'income_title'           => 'Total Income',
    'income_title_ps'        => 'د عاید مدیریت',

    'expenses_income_chart'  => 'Expenses & Income - Last 12 Months',
    'expenses_income_chart_ps'=> 'د تیر ۱۲ میاشتو مصرف او عاید',
];

/* ================= TRANSLATION FUNCTION ================= */
if (!function_exists('t')) {
    /**
     * Get translation by key and language
     *
     * @param string $key
     * @param array $translations
     * @param string|null $lang
     * @return string
     */
    function t(string $key, array $translations, ?string $lang = null): string {
        $lang = $lang ?? ($_SESSION['custom_lang'] ?? $_COOKIE['custom_lang'] ?? 'en');
        $keyWithLang = ($lang === 'ps') ? $key . '_ps' : $key;
        return $translations[$keyWithLang] ?? $translations[$key] ?? $key;
    }
}

/* ================= CONFIG RETURN ================= */
return [
    'lang'         => $defaultLang,
    'dir'          => in_array($defaultLang, $rtlLanguages) ? 'rtl' : 'ltr',
    'translations' => $translations,
];
