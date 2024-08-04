<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class Create_Question extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
    }

    public function display_result() {
        // Display the Question with the answers
        echo '<h1>Question</h1>';
        echo '<p>This is a test.</p>';
    }
}
?>
