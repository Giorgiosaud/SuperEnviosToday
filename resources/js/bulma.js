const animateCSS = function animateCSS(node, animationName, callback) {
    node.classList.add('animated', animationName)

    function handleAnimationEnd() {
        if (typeof callback === 'function') callback()
    }

    node.addEventListener('animationend', handleAnimationEnd)
}


document.addEventListener("DOMContentLoaded", function () {
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(notification => {
        animateCSS(notification, 'slideInDown', () => {
            setTimeout(() => {
                notifications.forEach(notificationPanel => {
                    notificationPanel.classList.remove('slideInDown');
                    notificationPanel.classList.add('slideOutUp');
                })
            }, 3000)
        })

        notification.querySelector('.delete').addEventListener('click', (event) => {
            event.target.parentElement.classList.remove('slideInDown');
            event.target.parentElement.classList.add('slideOutUp');
        })
    })
})
