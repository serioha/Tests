<?php
class DiSC_Activator {
    public static function activate() {
        DB_Manager::create_table();
    }
}

class DiSC_Deactivator {
    public static function deactivate() {
        DB_Manager::drop_table();
    }
}
register_activation_hook(__FILE__, array('DiSC_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('DiSC_Deactivator', 'deactivate'));
