<?php
class DiSC_Frontend_Base {
    protected $wpdb;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    // Additional common methods for frontend classes can be added here
}
?>
