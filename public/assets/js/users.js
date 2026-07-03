$(function() {
    // Simple HTML escaping to prevent XSS vulnerabilities when inserting data
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Main function to load and update the users table via AJAX
    function updateList(url, pushState) {
        if (typeof pushState === 'undefined') pushState = true;

        $.getJSON(url)
            .done(function(response) {
                var tbody = $('#users-tbody');
                tbody.empty();

                // Display empty state placeholder if no users are found
                if (response.users.length === 0) {
                    tbody.html('<tr><td colspan="7" class="empty-message">No users found.</td></tr>');
                } else {
                    var csrfToken = $('#csrf-token').val();
                    // Build table rows
                    $.each(response.users, function(i, user) {
                        var row = $('<tr>');
                        row.append('<td>' + escapeHtml(user.id) + '</td>');
                        row.append('<td><strong>' + escapeHtml(user.login) + '</strong></td>');
                        row.append('<td>' + escapeHtml(user.firstName) + '</td>');
                        row.append('<td>' + escapeHtml(user.lastName) + '</td>');
                        row.append('<td>' + escapeHtml(user.gender) + '</td>');
                        row.append('<td>' + escapeHtml(user.birthDate) + '</td>');
                        var actions = '<td class="actions">' +
                            '<a href="/users/' + user.id + '" class="btn-text btn-text-primary">View</a> ' +
                            '<a href="/users/' + user.id + '/edit" class="btn-text btn-text-warning">Edit</a> ' +
                            '<form method="post" action="/users/' + user.id + '/delete" style="display:inline;" onsubmit="return confirm(\'Delete this user?\')">' +
                            '<input type="hidden" name="_csrf_token" value="' + csrfToken + '">' +
                            '<button type="submit" class="btn-text btn-text-danger">Delete</button>' +
                            '</form>' +
                            '</td>';
                        row.append(actions);
                        tbody.append(row);
                    });
                }

                // Update sorting arrows in the table header
                $('thead a.sort-link').each(function() {
                    var column = $(this).data('column');
                    var label = $(this).data('label');
                    var newDir = (response.sort === column && response.dir === 'asc') ? 'desc' : 'asc';
                    var arrowChar = response.dir === 'asc' ? '↑' : '↓';
                    
                    var arrowHtml = (response.sort === column)
                        ? '<span class="sort-arrow">' + arrowChar + '</span>'
                        : '<span class="sort-arrow-hidden">↑</span>';
                        
                    $(this).attr('href', '?sort=' + column + '&dir=' + newDir + '&page=' + response.currentPage);
                    $(this).html(label + ' ' + arrowHtml);
                });

                // Render sliding window pagination with dots
                var pagination = $('#pagination');
                pagination.empty();
                if (response.pages > 1) {
                    var delta = 2;
                    var range = [];
                    for (var i = 1; i <= response.pages; i++) {
                        if (i == 1 || i == response.pages || (i >= response.currentPage - delta && i <= response.currentPage + delta)) {
                            range.push(i);
                        }
                    }
                    var rangeWithDots = [];
                    var l = null;
                    for (var i = 0; i < range.length; i++) {
                        var val = range[i];
                        if (l !== null) {
                            if (val - l === 2) {
                                rangeWithDots.push(l + 1);
                            } else if (val - l !== 1) {
                                rangeWithDots.push('...');
                            }
                        }
                        rangeWithDots.push(val);
                        l = val;
                    }

                    for (var j = 0; j < rangeWithDots.length; j++) {
                        var p = rangeWithDots[j];
                        var linkHtml;
                        if (p === '...') {
                            linkHtml = '<span class="page-item dots">...</span>';
                        } else if (p === response.currentPage) {
                            linkHtml = '<span class="page-item active">' + p + '</span>';
                        } else {
                            linkHtml = '<a href="?page=' + p + '&sort=' + response.sort + '&dir=' + response.dir + '" class="page-item" data-page="' + p + '">' + p + '</a>';
                        }
                        pagination.append(linkHtml);
                    }
                }

                if (pushState) {
                    var userUrl = url.replace('/api', '');
                    // If the server clamped the page to a valid range, rewrite the URL
                    // to show the corrected page number so the address bar stays accurate.
                    if (response.pageWasAdjusted) {
                        userUrl = userUrl.replace(/([?&]page=)\d+/, '$1' + response.currentPage);
                    }
                    history.pushState(null, '', userUrl);
                }
            })
            .fail(function() {
                alert('Failed to load users. Please try again.');
            });
    }

    // Delegated click handlers
    $('#users-container').on('click', '#pagination a, thead a', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        updateList('/api/users' + href);
    });

    $(window).on('popstate', function() {
        updateList('/api/users' + location.search, false);
    });
});