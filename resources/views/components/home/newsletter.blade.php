<section class="py-5">
    <div class="container-fluid">

        <div class="bg-secondary py-5 my-5 rounded-5"
            style="background: url('{{ asset('images/bg-leaves-img-pattern.png') }}') no-repeat;">
            <div class="container my-5">
                <div class="row">
                    <div class="col-md-6 p-5">
                        <div class="section-header">
                            <h2 class="section-title display-4">Get <span class="text-primary">25% Discount</span> on
                                your first purchase</h2>
                        </div>
                        <p>Your time is precious. Whether it’s groceries, cleaning, or laundry, let KANMA deliver it — fast.  Sign up now and enjoy 25% off your first purchase.</p>
                    </div>
                    <div class="col-md-6 p-5">
                        <form id="newsletterForm">
                            <div id="newsletterAlert"></div>
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control form-control-lg" name="name" id="name"
                                    placeholder="Name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control form-control-lg" name="email" id="email"
                                    placeholder="abc@mail.com" required>
                            </div>
                            <div class="form-check form-check-inline mb-3">
                                <label class="form-check-label" for="subscribe">
                                    <input class="form-check-input" type="checkbox" id="subscribe" value="subscribe">
                                    Subscribe to the newsletter</label>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-dark btn-lg">Submit</button>
                            </div>
                        </form>

                    </div>

                </div>

            </div>
        </div>

    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('newsletterForm');
    const alertBox = document.getElementById('newsletterAlert');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        alertBox.innerHTML = '';
        const formData = new FormData(form);
        fetch('/newsletter', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(async response => {
            if (response.status === 422) {
                const errorData = await response.json();
                let errors = Object.values(errorData.errors).flat().join('<br>');
                alertBox.innerHTML = `<div class='alert alert-danger mt-2'>${errors}</div>`;
                throw new Error('Validation failed');
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                alertBox.innerHTML = `<div class='alert alert-success mt-2'>${data.message}</div>`;
                form.reset();
            } else {
                alertBox.innerHTML = `<div class='alert alert-danger mt-2'>${data.message || 'Something went wrong.'}</div>`;
            }
        })
        .catch(error => {
            if (error.message !== 'Validation failed') {
                alertBox.innerHTML = `<div class='alert alert-danger mt-2'>Something went wrong. Please try again.';</div>`;
            }
        });
    });
});
</script>