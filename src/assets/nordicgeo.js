$(document).ready(function(){
    // Configuration for country select
    var country = {'select': $('.geography-select.country'), 'api': 'RegionsByCountry', 'target': '.geography-select.region'};

    // Configuration for region select
    var region = {'select': $('.geography-select.region'), 'api': 'PlacesByRegion', 'target': '.geography-select.place'};

    // Configuration for place select
    var place = {'select': $('.geography-select.place'), 'api': 'PostalCode', 'target': '.geography-select.postalcode'};

    // Iterate through the array of select configurations
    $.each([country, region, place], function(index, selectInfo){
        // Add an event listener for the change event on the current select element
        selectInfo.select.on('change', function(){
            // Get selected values from each select element
            var selectedCountry = country.select.val();
            var selectedRegion = region.select.val();
            var selectedPlace = place.select.val();

            // Check if the selected country is IS or FO
            if (selectedCountry === 'IS' || selectedCountry === 'FO') {
                // Hide the region
                $(country.target).closest('.form-group').hide();
            } else {
                // Show the region
                $(country.target).closest('.form-group').show();
            }

            // Perform an AJAX request to retrieve options based on selected values
            $.ajax({
                url: '/nordicgeo/get-options',
                method: 'GET',
                data: {
                    type: selectInfo.api,
                    country: selectedCountry,
                    region: selectedRegion,
                    place: selectedPlace
                },
                success: function(data){
                    // Update the target select element with the retrieved options
                    $(selectInfo.target)
                        .empty()
                        .html(data)
                        .trigger('change');
                },
                error: function(){
                    console.log('Error!');
                }
            });
        });
    });
});
