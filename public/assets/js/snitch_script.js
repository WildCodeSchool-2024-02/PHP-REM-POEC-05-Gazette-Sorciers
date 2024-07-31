    const vifdor = document.getElementById('vifdor');
    let vifdorWidth = vifdor.offsetWidth;
    let vifdorHeight = vifdor.offsetHeight;
    let margin = 100; 

    function getRandomPosition() {
        let maxX = window.innerWidth - vifdorWidth - margin;
        let maxY = window.innerHeight - vifdorHeight - margin;
        let randomX = Math.random() * maxX + margin / 2;
        let randomY = Math.random() * maxY + margin / 2;
        return { x: randomX, y: randomY };
    }

    function moveVifDor() {
        let newPos = getRandomPosition();
        vifdor.style.transform = `translate(${newPos.x}px, ${newPos.y}px)`;
    }

    function animateVifDor() {
        moveVifDor();
        setTimeout(animateVifDor, Math.random() * 2000 + 1000); 
    }

    vifdor.addEventListener('mouseover', moveVifDor);

    animateVifDor();

    window.addEventListener('resize', () => {
        vifdorWidth = vifdor.offsetWidth;
        vifdorHeight = vifdor.offsetHeight;
    });

    document.getElementById('vifdor').addEventListener('click', function() {
        window.location.href = '/easter-egg';
    });

    


