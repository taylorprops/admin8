// send csrf with every ajax request
var _token = $('meta[name="csrf-token"]').attr('content');
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': _token
    }
});


/*
function filter_array(value, index, self) {
    return self.indexOf(value) === index;
}
 USAGE
group_ids = ['a', 'b', 'c', 'c'];
group_ids = group_ids.filter(filter_array);
*/