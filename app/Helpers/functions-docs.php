<?php

function get_value($values, $id) {
    foreach($values as $value) {
        if($value['input_id'] == $id) {
            return $value['input_value'];
        }
    }
}

function get_value_radio_checkbox($values, $id) {
    foreach($values as $value) {
        if($value['input_id'] == $id) {
            if($value['input_value'] != '') {
                return 'checked="checked"';
            } else {
                return '';
            }

        }
    }
}

function address_type($val) {
    if($val == 'Full Address') {
        return 'full';
    } else if($val == 'Street Address') {
        return 'street';
    } else if($val == 'City') {
        return 'city';
    } else if($val == 'State') {
        return 'state';
    } else if($val == 'County') {
        return 'county';
    } else if($val == 'Zip Code') {
        return 'zip';
    }
}
?>
