// Filtered By Style

document.addEventListener("DOMContentLoaded", function() {
    const paintingsContainer = document.getElementById('paintingsContainer');
    const styleSelect = document.getElementById('styleSelect');

    fetch('fetch_paintings.php')
        .then(response => response.json())
        .then(data => {
            displayPaintings(data);
            
            styleSelect.addEventListener('change', () => {
                const selectedStyle = styleSelect.value;
                const filteredData = selectedStyle ? data.filter(p => p.Style === selectedStyle) : data;
                displayPaintings(filteredData);
            });
        });

    function displayPaintings(paintings) {
        paintingsContainer.innerHTML = '';
        paintings.forEach(painting => {
            const card = `
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="row no-gutters">
                            <div class="col-md-4">
                                <img src="data:image/jpeg;base64,${painting.Image}" class="card-img" alt="${painting.Title}">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title">${painting.Title}</h5>
                                    <p class="card-text">Year: ${painting.Finished}</p>
                                    <p class="card-text">Media: ${painting.Media}</p>
                                    <p class="card-text">Style: ${painting.Style}</p>
                                    <p class="card-text">Artist: ${painting.ArtistName}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            paintingsContainer.innerHTML += card;
        });
    }
});

//Filtered by Artist 

document.addEventListener("DOMContentLoaded", function() {
    const paintingsContainer = document.getElementById('paintingsContainer');
    const artistSelect = document.getElementById('artistSelect');
    const styleSelect = document.getElementById('styleSelect');

    fetch('fetch_paintings.php')
        .then(response => response.json())
        .then(data => {
            displayPaintings(data);

            // Filter by Artist
            artistSelect.addEventListener('change', () => {
                const selectedArtistID = artistSelect.value;
                const filteredData = selectedArtistID ? data.filter(p => p.ArtistID == selectedArtistID) : data;
                displayPaintings(filteredData);
            });

            // Filter by Style if it exists
            if (styleSelect) {
                styleSelect.addEventListener('change', () => {
                    const selectedStyle = styleSelect.value;
                    const filteredData = selectedStyle ? data.filter(p => p.Style === selectedStyle) : data;
                    displayPaintings(filteredData);
                });
            }
        });

    function displayPaintings(paintings) {
        paintingsContainer.innerHTML = '';
        paintings.forEach(painting => {
            const card = `
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="row no-gutters">
                            <div class="col-md-4">
                                <img src="data:image/jpeg;base64,${painting.Image}" class="card-img" alt="${painting.Title}">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h5 class="card-title">${painting.Title}</h5>
                                    <p class="card-text">Year: ${painting.Finished}</p>
                                    <p class="card-text">Media: ${painting.Media}</p>
                                    <p class="card-text">Style: ${painting.Style}</p>
                                    <p class="card-text">Artist: ${painting.ArtistName}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            paintingsContainer.innerHTML += card;
        });
    }
});