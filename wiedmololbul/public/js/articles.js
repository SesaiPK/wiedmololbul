document.addEventListener('DOMContentLoaded', () => {
    const listElement = document.getElementById('articles-list');

    // Pobieranie danych z API
    fetch('/api/article')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Iterujemy przez artykuły i dodajemy je do listy
            data.forEach(article => {
                const listItem = document.createElement('li');
                listItem.textContent = `${article.id}: ${article.title}`;
                listElement.appendChild(listItem);
            });
        })
        .catch(error => {
            console.error('Błąd podczas pobierania danych:', error);
        });
});
