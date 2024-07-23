

const searchInput = document.getElementById('search-input');
const suggestionsContainer = document.getElementById('suggestions');

async function fetchAndDisplaySuggestions() {
    const query = searchInput.value;
    if (query.length > 2) {
        const suggestions = await fetchSuggestions(query);
        displaySuggestions(suggestions);
    } else {
        suggestionsContainer.innerHTML = '';
    }
}

searchInput.addEventListener('input', fetchAndDisplaySuggestions);

async function fetchSuggestions(query) {
    try {
        const response = await fetch(`/search?query=${encodeURIComponent(query)}`);
        const suggestions = await response.json();
        return suggestions;
    } catch (error) {
        console.error('Error fetching suggestions:', error);
        return [];
    }
}

function displaySuggestions(suggestions) {
    suggestionsContainer.innerHTML = '';
    suggestions.forEach(suggestion => {
        const suggestionElement = document.createElement('div');
        suggestionElement.classList.add('suggestion');

        const nameSpan = document.createElement('span');
        nameSpan.textContent = suggestion.name;
        nameSpan.className = 'suggestion-name';
        suggestionElement.appendChild(nameSpan);

        const typeSpan = document.createElement('span');
        typeSpan.textContent = suggestion.type === 'category' ? ' (Category)' : ' (Topic)';
        typeSpan.className = 'suggestion-type';
        typeSpan.style.float = 'right';
        suggestionElement.appendChild(typeSpan);

        suggestionElement.dataset.id = suggestion.id;
        suggestionElement.dataset.type = suggestion.type;
        suggestionElement.addEventListener('click', () => {
            if (suggestionElement.dataset.type === 'category') {
                window.location.href = `/topics?id=${suggestionElement.dataset.id}`;
            } else if (suggestionElement.dataset.type === 'topic') {
                window.location.href = `/topics/show?id=${suggestionElement.dataset.id}`;
            }
        });
        suggestionsContainer.appendChild(suggestionElement);
    });
}

// Masque les suggestions lorsqu'on clique à l'extérieur de la barre de recherche ou des suggestions
document.addEventListener('click', (event) => {
    if (!searchInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
        suggestionsContainer.innerHTML = '';
    }
});
searchInput.addEventListener('focus', fetchAndDisplaySuggestions);

// Empêche la propagation du clic lorsqu'on cliquez sur la barre de recherche ou les suggestions
searchInput.addEventListener('click', (event) => {
    event.stopPropagation();
});

suggestionsContainer.addEventListener('click', (event) => {
    event.stopPropagation();
});

