// 관리자 정렬 UI — jQuery UI sortable 대체 (드래그 앤 드롭)

function ghInitSortable(root) {
	var list = typeof root === 'string' ? document.querySelector(root) : root;
	if (!list || list.dataset.sortableInit === '1') {
		return;
	}
	list.dataset.sortableInit = '1';
	var dragEl = null;
	var placeholder = document.createElement('li');
	placeholder.className = 'ui-state-highlight';

	list.querySelectorAll(':scope > li').forEach(function (li) {
		li.setAttribute('draggable', 'true');
	});

	list.addEventListener('dragstart', function (e) {
		var li = e.target.closest('li');
		if (!li || li.parentElement !== list) {
			return;
		}
		dragEl = li;
		e.dataTransfer.effectAllowed = 'move';
		e.dataTransfer.setData('text/plain', '');
		setTimeout(function () {
			li.style.opacity = '0.4';
		}, 0);
	});

	list.addEventListener('dragend', function () {
		if (dragEl) {
			dragEl.style.opacity = '';
		}
		if (placeholder.parentNode) {
			placeholder.parentNode.removeChild(placeholder);
		}
		dragEl = null;
	});

	list.addEventListener('dragover', function (e) {
		e.preventDefault();
		if (!dragEl) {
			return;
		}
		var after = ghSortableGetDragAfterElement(list, e.clientY);
		if (after == null) {
			list.appendChild(placeholder);
		} else {
			list.insertBefore(placeholder, after);
		}
	});

	list.addEventListener('drop', function (e) {
		e.preventDefault();
		if (!dragEl) {
			return;
		}
		if (placeholder.parentNode === list) {
			list.insertBefore(dragEl, placeholder);
			placeholder.parentNode.removeChild(placeholder);
		}
	});
}

function ghSortableGetDragAfterElement(container, y) {
	var els = [].slice.call(container.querySelectorAll(':scope > li:not(.ui-state-highlight)'));
	var closest = { offset: Number.NEGATIVE_INFINITY, element: null };
	els.forEach(function (child) {
		if (child === container.querySelector('li[style*="opacity: 0.4"]')) {
			return;
		}
		var box = child.getBoundingClientRect();
		var offset = y - box.top - box.height / 2;
		if (offset < 0 && offset > closest.offset) {
			closest = { offset: offset, element: child };
		}
	});
	return closest.element;
}

// 동적 주입된 정렬 목록 재초기화
function ghInitSortableIn(container) {
	if (!container) {
		return;
	}
	container.querySelectorAll('.sortable, [id^="sortable"]').forEach(function (el) {
		el.dataset.sortableInit = '';
		ghInitSortable(el);
	});
}
