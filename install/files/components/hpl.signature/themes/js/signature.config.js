interact('.digital-signature')
    .draggable({
        inertia: true,
        restrict: {
            restriction: "parent",
            endOnly: true,
            elementRect: {top: 0, left: 0, bottom: 1, right: 1}
        },
        autoScroll: true,
        onmove: function (event) {
            var canvas = document.createElement('canvas');
            var contex = canvas.getContext('2d');
            const image = document.getElementsByClassName('signature-item');x
            var target = event.target;
            var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
            var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

            target.style.webkitTransform = target.style.transform = 'translate(' + x + 'px, ' + y + 'px)';
            target.style.border = '2px dashed #ddd';
            target.classList.remove('digital-signature--remove')

            target.setAttribute('data-x', x);
            target.setAttribute('data-y', y);
            // console.log('Coordinate X,Y(' + event.pageX + ', ' + event.pageY + ')')
            // document.getElementById("leftValue").value = event.pageX;
            // document.getElementById("topValue").value = event.pageY;
        },
        onend: function (event) {
            var target = event.target;
            target.classList.add('digital-signature--remove')
        }
    })
    .resizable({
        inertia: true,
        edges: {left: true, right: true, bottom: true, top: true},
        onmove: function (event) {
            var target = event.target;
            var x = (parseFloat(target.getAttribute('data-x')) || 0);
            var y = (parseFloat(target.getAttribute('data-y')) || 0);

            x += event.deltaRect.left;
            y += event.deltaRect.top;

            target.style.width = event.rect.width + 'px';
            target.style.height = event.rect.height + 'px';
            target.style.webkitTransform = target.style.transform = 'translate(' + x + 'px,' + y + 'px)';
            target.setAttribute('data-x', x);
            target.setAttribute('data-y', y);
            heightValue = event.rect.height;
            document.getElementById("widthValue").value = event.rect.width;
            document.getElementById("heightValue").value = event.rect.height;

        },
    })
