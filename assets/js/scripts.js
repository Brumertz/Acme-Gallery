document.addEventListener("DOMContentLoaded", function() {
    const paintingsContainer = document.getElementById('paintingsContainer');
    const artistSelect = document.getElementById('artistSelect');
    const styleSelect = document.getElementById('styleSelect');

    fetch('fetch_paintings.php')
        .then(response => response.json())
        .then(data => {
            console.log("Fetched Data:", data); // Log fetched data to check if it's received correctly
            displayPaintings(data);

            // Filter by Artist
            if (artistSelect) {
                artistSelect.addEventListener('change', () => {
                    const selectedArtistID = artistSelect.value;
                    console.log("Selected Artist ID:", selectedArtistID); // Log selected artist ID
                    const filteredData = selectedArtistID ? data.filter(p => p.ArtistID == selectedArtistID) : data;
                    displayPaintings(filteredData);
                });
            }

            // Filter by Style
            if (styleSelect) {
                styleSelect.addEventListener('change', () => {
                    const selectedStyle = styleSelect.value;
                    console.log("Selected Style:", selectedStyle); // Log selected style
                    const filteredData = selectedStyle ? data.filter(p => p.Style === selectedStyle) : data;
                    displayPaintings(filteredData);
                });
            }
        })
        .catch(error => console.error("Error fetching paintings:", error));

    function displayPaintings(paintings) {
        paintingsContainer.innerHTML = '';
        console.log("Displaying Paintings:", paintings); // Log paintings being displayed

        if (paintings.length === 0) {
            paintingsContainer.innerHTML = '<p class="alert alert-warning">No paintings found.</p>';
        } else {
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
    }
});

