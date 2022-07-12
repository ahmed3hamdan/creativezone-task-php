$(document).ready(function () {
    var contactForm = $(document.forms.contact_form);
    var contactFormButton = contactForm.find("button[type=submit]");
    var alertWrapper = $("#alert-wrapper");
    // validate form before submission
    contactForm.validate({
        errorClass: "text-danger",
        submitHandler: function () {
            $.ajax({
                url: "./api/contact.php",
                type: "POST",
                enctype: "multipart/form-data",
                processData: false,
                contentType: false,
                data: new FormData(document.forms.contact_form),
                beforeSend: function () {
                    alertWrapper.addClass("d-none");
                    contactFormButton.attr("disabled", "disabled");
                },
                success: function () {
                    alertWrapper.html("<div class=\"alert alert-success\">Request sent successfully.</div>");
                    contactForm.trigger("reset");
                },
                error: function () {
                    alertWrapper.html("<div class=\"alert alert-danger\">Request failed.</div>");
                },
                complete: function () {
                    alertWrapper.removeClass("d-none");
                    contactFormButton.removeAttr("disabled");
                }
            });
        }
    });
});