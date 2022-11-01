var picture = "";
var html_markup = "";
var container = "";
var netid = "";
var profiles_url = 'https://profiles.ucr.edu';
var profiles_api = '/api/profile/';

jQuery(document).ready(function() {
    jQuery('.media-contact-profile').each(function(index) {
        container = jQuery(this);
        netid = container.attr('data-netid');

        jQuery.ajax({
            url: profiles_url + profiles_api + netid,
            crossDomain: true,
            dataType: 'json',
            method: 'GET',
            cache: false,
            htmlContainer: container
        }).done(function(people_data) {
            var media_info = { twitter: "", facebook: "", linkedin: "", instagram: "", snapchat: "", website: "", email: "", phone: "" };
            if((people_data.twitter !== "") && (people_data.twitter !== null)) {
                media_info.twitter = '<span class="media-twitter"><a href="https://twitter.com/' + people_data.twitter + '" target="_blank"><i class="mdi mdi-twitter"></i> Tweet</a></span>';
            }
            if((people_data.facebook !== "") && (people_data.facebook !== null)) {
                media_info.facebook = '<span class="media-facebook"><a href="https://facebook.com/' + people_data.facebook + '" target="_blank"><i class="mdi mdi-facebook"></i> Facebook</a></span>';
            }
            if((people_data.linkedIn !== "") && (people_data.linkedIn !== null)) {
                media_info.linkedin = '<span class="media-linkedin"><a href="https://linkedin.com/' + people_data.linkedIn + '" target="_blank"><i class="mdi mdi-linkedin"></i> LinkedIn</a></span>';
            }
            if((people_data.instagram !== "") && (people_data.instagram !== null)) {
                media_info.instagram = '<span class="media-instagram"><a href="https://instagram.com/' + people_data.instagram + '" target="_blank"><i class="mdi mdi-instagram"></i> Instagram</a></span>';
            }
            if((people_data.snapchat !== "") && (people_data.snapchat !== null)) {
                media_info.snapchat = '<span class="media-snapchat"><a href="https://snapchat.com/' + people_data.snapchat + '" target="_blank"><i class="mdi mdi-snapchat"></i> SnapChat</a></span>';
            }
            if((people_data.website !== "") && (people_data.website !== null)) {
                media_info.website = '<span class="media-website"><a href="' + people_data.website + '" target="_blank"><i class="mdi mdi-web"></i> Website</a></span>';
            }
            if((people_data.email !== "") && (people_data.email !== null)) {
                media_info.email = '<span class="media-email"><a href="mailto:' + people_data.email + '" target="_blank"><i class="mdi mdi-email"></i> Email</a></span>';
            }
            if((people_data.phone !== "") && (people_data.phone !== null)) {
                media_info.phone = '<span class="media-phone"><i class="mdi mdi-phone"></i> ' + people_data.phone + '</span>';
            }

            var media_info_string = "";
            jQuery.each(media_info, function(index, value) {
                if(value !== "") { media_info_string += value; }
            });

            html_markup = '<div class="media-object">'+
                '<div class="media-object-section align-self-middle"><div class="thumbnail"><img src="' + profiles_url + people_data.profilePicture + '" alt="' + people_data.name + '" /></div></div>' +
                '<div class="media-object-section main-section align-self-middle">' +
                    '<h4>' + people_data.name + '</h4>' +
                    '<span class="media-contact-title">' + people_data.title + '</span>' +
                    '<div class="media-contact-info">' + media_info_string + '</div>' +
                '</div>' +
            '</div>';

            this.htmlContainer.html(html_markup);
        });
    });
});
