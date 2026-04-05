// Confirmation de suppression
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?')) {
        window.location.href = `../api/delete_transaction.php?id=${id}`;
    }
}

// Validation du formulaire
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--danger)';
                } else {
                    field.style.borderColor = 'var(--border)';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires');
            }
        });
    });
});

// Animation des nombres
function animateNumber(element, target) {
    let current = 0;
    const increment = target / 50;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target.toFixed(2);
            clearInterval(timer);
        } else {
            element.textContent = current.toFixed(2);
        }
    }, 20);
}

// Graphique simple (peut être amélioré avec Chart.js)
function createSimpleChart(data, container) {
    const total = data.reduce((sum, item) => sum + item.value, 0);
    let html = '<div class="chart-container">';
    
    data.forEach(item => {
        const percentage = (item.value / total * 100).toFixed(1);
        html += `
            <div class="chart-bar">
                <div class="chart-label">${item.label}</div>
                <div class="chart-progress">
                    <div class="chart-fill" style="width: ${percentage}%"></div>
                </div>
                <div class="chart-value">${percentage}%</div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

// Filtrage de tableau
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    
    input.addEventListener('keyup', function() {
        const filter = input.value.toLowerCase();
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        }
    });
}
