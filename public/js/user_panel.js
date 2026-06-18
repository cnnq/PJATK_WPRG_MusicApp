document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.nav-button');
    const tabs = document.querySelectorAll('.tab');

    function showSection(clickedButton, tabClass) {
        tabs.forEach(tab => {
            if (tab.classList.contains(tabClass)) {
                tab.style.display = 'block';
            } else {
                tab.style.display = 'none';
            }
        });

        buttons.forEach(button => {
            if (button === clickedButton) {
                button.classList.add('active');
            } else {
                button.classList.remove('active');
            }
        });
    }


    buttons[0].addEventListener('click', function () {
        showSection(this, 'profile');
    })
    buttons[1].addEventListener('click', function () {
        showSection(this, 'songs');
    })

    // Default view
    showSection(buttons[0], 'profile');
});

/**
 * Preview image
 * @param input input tag
 * @param targetId id of the image tag
 */
function previewImage(input, targetId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(targetId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
