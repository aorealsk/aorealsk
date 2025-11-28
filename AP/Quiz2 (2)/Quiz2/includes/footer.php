</main>
<footer>
    <p>&copy; <?= date("Y") ?> PHP Quiz</p>

    <script>
        const lists = document.querySelectorAll('ul.sortable');

        if (lists.length > 0) {

            lists.forEach(list => {
                let dragItem = null;

                list.querySelectorAll('li').forEach(item => {

                    item.addEventListener('dragstart', () => {
                        dragItem = item;
                        item.classList.add('dragging');
                    });

                    item.addEventListener('dragend', () => {
                        dragItem = null;
                        item.classList.remove('dragging');
                    });

                    item.addEventListener('dragover', (e) => {
                        e.preventDefault();

                        const after = [...list.children].find(li => {
                            return e.clientY <= li.getBoundingClientRect().top + li.offsetHeight / 2;
                        });

                        if (after) list.insertBefore(dragItem, after);
                        else list.appendChild(dragItem);
                    });

                });
            });

        }
    </script>

</footer>
</body>
</html>
