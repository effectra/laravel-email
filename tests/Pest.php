<?php

test('tables created', function () {
    $this->assertTrue(Schema::hasTable('email_messages'));
    $this->assertTrue(Schema::hasTable('email_templates'));
});