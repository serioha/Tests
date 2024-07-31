<?php
class DiSC_Tests {
    private $wpdb;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    public function get_tests() {
        // Get a list of available tests
        return array(
            'Test 1' => 'test_1',
            'Test 2' => 'test_2',
            'Test 3' => 'test_3'
        );
    }
}
?>
