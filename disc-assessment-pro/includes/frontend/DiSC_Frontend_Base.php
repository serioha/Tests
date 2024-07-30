<?php
abstract class DiSC_Frontend_Base {
    protected $wpdb;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    // Base class for frontend functionality
}
?>
