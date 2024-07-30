<?php
abstract class DiSC_Admin_Base {
    protected $wpdb;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    // Base class for admin functionality
}
?>
