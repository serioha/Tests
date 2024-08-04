<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class Create_Question extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
    }

    public function display_result() {
        // Display the Question with the answers
        echo '<h1>Question</h1>';
        echo '<p>This is a question. The call from the `display_result` method.</p>';
    }

    public function create_question(){
        // Create Question
        echo '<p>These are the answers. The call from the `create_question` method.</p>';
    }

    public function render() {

        echo '<h1>The rendered Question</h1>';
        echo '<p>The question. This is the call from `render` method</p>';
    }
}
?>
