// modals

const modal = function () {

    let toggleIndicator = document.querySelector("#site-navigation");
    let body = document.querySelector('body');

    function init() {
        document.querySelector('.menu-toggle').addEventListener('click', toggleModal);
    }
    
    function toggleModal() {
        // set timeout so function runs after theme functions
        setTimeout(function(){
            if (toggleIndicator.classList.contains("toggled")) {
                body.classList.add('modal-open');
            }
            else {
                body.classList.remove('modal-open');
            }
        }, 0);
    }

    return {
        init: init
    }
}

const modalModule = modal();
modalModule.init();