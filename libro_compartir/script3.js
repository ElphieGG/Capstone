document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".bid-form").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent normal form submission
            
            let formData = new FormData(this);

            fetch("place_bid.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Bid Placed!",
                        text: data.message,
                        confirmButtonColor: "#3085d6"
                    }).then(() => {
                        location.reload(); // Reload page to update bid list
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: data.message,
                        confirmButtonColor: "#d33"
                    });
                }
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire({
                    icon: "error",
                    title: "Error!",
                    text: "Something went wrong. Please try again.",
                    confirmButtonColor: "#d33"
                });
            });
        });
    });
});
