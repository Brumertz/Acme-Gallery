<?php include 'includes/header.php'; ?>

<div class="container mt-5">
    <h2>Search Painting by Title</h2>
    <div class="form-group">
        <input type="text" id="searchInput" class="form-control" placeholder="Enter painting title">
    </div>
    <div id="searchResults" class="row mt-4">
        <!-- Search results will be populated here by JavaScript -->
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('input', function() {
    const searchQuery = this.value.toLowerCase();
    fetch('fetch_paintings.php')
        .then(response => response.json())
        .then(data => {
            const filteredData = data.filter(p => p.Title.toLowerCase().includes(searchQuery));
            displayResults(filteredData);
        });

    function displayResults(paintings) {
        const searchResults = document.getElementById('searchResults');
        searchResults.innerHTML = '';

        if (paintings.length === 0) {
            searchResults.innerHTML = '<div class="col-12"><p class="alert alert-warning">No paintings found.</p></div>';
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
                searchResults.innerHTML += card;
            });
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>