(function () {
  'use strict';

  const TABLE_SELECTOR = '[data-list-table]';

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll(TABLE_SELECTOR).forEach(initTable);
    initDetailLinks();
    initResetButtons();
  });

  function parsePositiveInt(value, fallback) {
    const parsed = Number.parseInt(value, 10);
    if (Number.isFinite(parsed) && parsed > 0) return parsed;
    return fallback;
  }

  function initTable(table) {
    const tbody = table.tBodies[0];
    if (!tbody) return;

    const wrapper = table.closest('[data-table-wrap]');
    const paginationContainers = wrapper
      ? Array.from(wrapper.querySelectorAll('[data-pagination]'))
      : [];

    const rowsPerPage = parsePositiveInt(table.dataset.rowsPerPage, 25);
    const emptyMessageText = table.dataset.emptyMessage || 'Ingen treff.';

    const allRows = Array.from(tbody.rows);
    const placeholderRow = allRows.find(row => row.dataset.emptyRow === 'true');
    const dataRows = allRows.filter(row => row.dataset.emptyRow !== 'true');

    if (placeholderRow) {
      placeholderRow.remove();
    }

    const firstHeaderRow = table.tHead && table.tHead.rows.length
      ? table.tHead.rows[0]
      : null;
    const columnCount = firstHeaderRow
      ? firstHeaderRow.cells.length
      : (dataRows[0]?.cells.length ?? 1);

    const noResultsRow = document.createElement('tr');
    noResultsRow.className = 'table-empty-row';
    noResultsRow.innerHTML = `<td colspan="${columnCount}">${placeholderRow ? placeholderRow.textContent.trim() : emptyMessageText}</td>`;

    const filters = Array.from(table.querySelectorAll('input[data-filter-column]'))
      .map(input => {
        const column = Number.parseInt(input.dataset.filterColumn, 10);
        if (!Number.isFinite(column)) return null;
        return {
          input,
          column,
          mode: (input.dataset.filterMode || 'contains').toLowerCase(),
          value: '',
          rawValue: '',
          param: buildFilterParamName(table, input, column)
        };
      })
      .filter(Boolean);

    const url = new URL(window.location.href);
    filters.forEach(filter => {
      const existing = url.searchParams.get(filter.param);
      if (existing !== null) {
        filter.rawValue = existing;
        filter.value = existing.toLowerCase().trim();
        filter.input.value = existing;
      }
    });

    let currentPage = 1;
    let filteredRows = dataRows.slice();

    filters.forEach(filter => {
      filter.input.addEventListener('input', () => {
        filter.rawValue = filter.input.value || '';
        filter.value = filter.rawValue.toLowerCase().trim();
        currentPage = 1;
        syncUrlParams(url, filters);
        applyFilters();
      });
    });

    syncUrlParams(url, filters);

    function applyFilters() {
      filteredRows = dataRows.filter(row => {
        return filters.every(filter => {
          if (!filter.value) return true;
          const cell = row.cells[filter.column];
          if (!cell) return false;
          const text = cell.textContent.toLowerCase();
          switch (filter.mode) {
            case 'startswith':
              return text.startsWith(filter.value);
            case 'equals':
              return text === filter.value;
            default:
              return text.includes(filter.value);
          }
        });
      });
      renderPage();
    }

    function renderPage() {
      dataRows.forEach(row => {
        row.style.display = 'none';
      });

      const total = filteredRows.length;
      const totalPages = total === 0 ? 1 : Math.ceil(total / rowsPerPage);
      currentPage = Math.min(Math.max(currentPage, 1), totalPages);

      if (total === 0) {
        if (!noResultsRow.parentNode) {
          tbody.appendChild(noResultsRow);
        }
      } else {
        if (noResultsRow.parentNode) {
          noResultsRow.remove();
        }
        const startIndex = (currentPage - 1) * rowsPerPage;
        const visibleRows = filteredRows.slice(startIndex, startIndex + rowsPerPage);
        visibleRows.forEach(row => {
          row.style.display = '';
        });
      }

      updatePagination(total, totalPages);
    }

    function updatePagination(total, totalPages) {
      paginationContainers.forEach(container => {
        container.innerHTML = '';

        if (total === 0) {
          const info = document.createElement('span');
          info.className = 'pagination-info';
          info.textContent = 'Ingen treff';
          container.appendChild(info);
          return;
        }

        const prev = createButton('Forrige', () => changePage(currentPage - 1));
        prev.disabled = currentPage === 1;

        const info = document.createElement('span');
        info.className = 'pagination-info';
        const labelText = document.createTextNode('Side ');

        const pageInput = document.createElement('input');
        pageInput.type = 'number';
        pageInput.inputMode = 'numeric';
        pageInput.min = '1';
        pageInput.max = String(totalPages);
        pageInput.value = String(currentPage);
        pageInput.className = 'pagination-page-input';
        pageInput.setAttribute('aria-label', 'Hopp til side');
        pageInput.disabled = totalPages === 1;

        const suffixText = document.createTextNode(` av ${totalPages}`);

        const commitPage = () => {
          const target = Number.parseInt(pageInput.value, 10);
          if (Number.isNaN(target)) {
            pageInput.value = String(currentPage);
            return;
          }
          changePage(target);
        };

        pageInput.addEventListener('change', commitPage);
        pageInput.addEventListener('keydown', event => {
          if (event.key === 'Enter') {
            event.preventDefault();
            commitPage();
          }
        });
        pageInput.addEventListener('focus', () => {
          pageInput.select();
        });
        pageInput.addEventListener('blur', () => {
          pageInput.value = String(currentPage);
        });

        info.append(labelText, pageInput, suffixText);

        const next = createButton('Neste', () => changePage(currentPage + 1));
        next.disabled = currentPage === totalPages;

        container.append(prev, info, next);
      });
    }

    function createButton(label, handler) {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'pagination-btn';
      button.textContent = label;
      button.addEventListener('click', handler);
      return button;
    }

    function changePage(targetPage) {
      const totalPages = Math.max(1, Math.ceil(filteredRows.length / rowsPerPage));
      const clamped = Math.min(Math.max(targetPage, 1), totalPages);
      if (clamped === currentPage) return;
      currentPage = clamped;
      renderPage();
    }

    applyFilters();
  }

  function buildFilterParamName(table, input, column) {
    const base = table.id || input.name || 'tbl';
    return `${base}_f${column}`;
  }

  function syncUrlParams(url, filters) {
    filters.forEach(filter => {
      if (filter.rawValue && filter.rawValue.trim() !== '') {
        url.searchParams.set(filter.param, filter.rawValue);
      } else {
        url.searchParams.delete(filter.param);
      }
    });
    const searchString = url.searchParams.toString();
    const newUrl = url.pathname + (searchString ? `?${searchString}` : '') + (url.hash || '');
    window.history.replaceState(null, '', newUrl);
  }

  function initDetailLinks() {
    document.querySelectorAll('[data-detail-param][data-detail-id]').forEach(link => {
      link.addEventListener('click', event => {
        const param = link.dataset.detailParam;
        const id = link.dataset.detailId;
        const base = link.dataset.detailBase || link.href;
        if (!param || !id || !base) return;

        event.preventDefault();

        const currentUrl = new URL(window.location.href);
        const targetUrl = new URL(base, currentUrl.origin);
        targetUrl.search = '';

        currentUrl.searchParams.forEach((value, key) => {
          if (key !== param) {
            targetUrl.searchParams.set(key, value);
          }
        });

        targetUrl.searchParams.set(param, id);

        const anchorId = link.dataset.detailAnchor || '';
        if (anchorId) {
          targetUrl.hash = `#${anchorId}`;
        } else if (link.hash) {
          targetUrl.hash = link.hash;
        }

        window.location.href = targetUrl.toString();
      });
    });
  }

  function initResetButtons() {
    document.querySelectorAll('[data-reset-table]').forEach(button => {
      button.addEventListener('click', event => {
        event.preventDefault();
        const target = button.getAttribute('href') || button.dataset.resetTable;
        if (!target) return;
        const baseUrl = new URL(target, window.location.origin);
        window.location.href = baseUrl.pathname + baseUrl.search + baseUrl.hash;
      });
    });
  }
})();
