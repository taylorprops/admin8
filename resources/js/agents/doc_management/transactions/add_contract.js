if (document.URL.match(/add_contract/)) {

    $(document).ready(function() {


            let address_search_input = document.getElementById('address_search_input');
            let places = new google.maps.places.Autocomplete(address_search_input);
            google.maps.event.addListener(places, 'place_changed', function() {
                var address = places.getPlace();
                let street_number = address.address_components[0].long_name;
                let street_name = address.address_components[1].long_name;
                let city = address.address_components[2].long_name;
                let county = address.address_components[4].long_name;
                let state = address.address_components[5].long_name;
                let zip = address.address_components[7].long_name;
                console.log(street_number, street_name, city, state, county, zip, state);
            });


    });

}
