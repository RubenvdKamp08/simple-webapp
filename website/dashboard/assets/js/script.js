//Read the image to datauri and append the daturi to the image tag so the uploaded image will instantly be showed on the page.
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#product-image')
                .attr('src', e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }
}

//Check if product category changes and search for the subcategories that matches with the normal category. Do this function when the page is directly loaded.
$(document).ready(function() {
    var levelClass = $('#category').find('option:selected').attr('class');
    $('#subcategory option').each(function () {
        var self = $(this);
        if (self.hasClass(levelClass)) {
            //If subcategory option matches with the category, show the subcategory in the subcategory selectbox.
            self.show();
            $('#none').show();
        } else {
            //If subcategory option does not match the category option class, hide the element and set the standard option to selected.
            if(self != $('#none')) {
                self.hide();
            }
            $('#none').show();
            // $('#none').prop('selected', true);
        }
    });
});

//Check if product category changes and search for the subcategories that matches with the normal category.
$(function(){
    $("#category").on("change",function(){
        var levelClass = $('#category').find('option:selected').attr('class');
        //Loop through all subcategory options.
        $('#subcategory option').each(function () {
            var self = $(this);
            if (self.hasClass(levelClass)) {
                //If subcategory option matches with the category, show the subcategory in the subcategory selectbox.
                self.show();
                $('#none').show();
            } else {
                //If subcategory option does not match the category option class, hide the element and set the standard option to selected.
                if(self != $('#none')) {
                    self.hide();
                }
                $('#none').show();
                $('#none').prop('selected', true);
            }
        });
    });
});