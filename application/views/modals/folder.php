<div class="modal fade" id="folder-tree" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal Dialog Scrollable</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="folder-tree-wrapper">
                    <ul class="folder-tree" id="folderTree">
                        
                    </ul>
                </div>

                <script>
                    const folders = <?=json_encode($folders ?? [])?>;
                    // Example structured array
                    let structuredArray = folderStructure(folders);


                    function folderStructure(folders, parent = null) {
                        const result = [];

                        for (let i in folders) {
                            if (folders[i]['parent_folder_id'] == parent) {
                                children = folderStructure(folders, folders[i]['folder_id']);

                                if (children) {
                                    folders[i]['children'] = children;
                                }

                                result.push(folders[i]);
                            }
                        }

                        return result;
                    }
                    
                    // Function to render the folder structure
                    function renderFolders(expanded) {
                        var folderTree = document.getElementById('folderTree');
                        folderTree.innerHTML = '';

                        function generateHTML(folder, expanded = false) {
                            const folderEl = document.getElementById(folder.parent_folder_id);
                            const li = folderEl ? folderEl : document.createElement('li');
                            li.id = folder.folder_id;
                            console.log(folderEl, folder.parent_folder_id)
                            
                            li.innerHTML = `<div data-target="f-${folder.folder_id}" class="d-flex justify-content-between">
                                                <div>
                                                    <i class="bi bi-folder-fill"></i>
                                                    ${folder.folder_name + ' (' + (folder.children ? folder.children.length : 0) + ')'}
                                                </div>

                                                <div id='operations' data-id='${folder['folder_id']}'>
                                                    <i onclick="createFolder(${folder['folder_id']})" id="createFolder" class="text-primary bi bi-folder-plus"></i>
                                                    <i onclick="createFolder()" id="deleteFolder" class="text-danger bi bi-folder-minus"></i> 
                                                </div>
                                            </div>
                                            `;

                            li.onclick = function(e) {
  
                                if (e.target?.offsetParent?.id == folder.folder_id && e.target.tagName != 'I') {
                                    li.classList.toggle('expanded');
                                }
                            }

                            if (folder.children) {
                                var arrow = document.createElement('div');
                                arrow.className = 'arrow';
                                arrow.innerHTML = '<i class="bi bi-chevron-down"></i>';
                                li.appendChild(arrow);

                                var ul = document.createElement('ul');
                                ul.className = 'collapsed';
                                folder.children.forEach(function(child) {
                                    ul.appendChild(generateHTML(child, expanded));
                                });
                                li.appendChild(ul);
                            }

                            return li;
                        }

                        structuredArray.forEach(function(folder) {
                            folderTree.appendChild(generateHTML(folder, expanded));
                        });
                    }

                    // Function to create a new folder
                    function createFolder(parent_folder_id=null) {
                        var newFolder = {
                            folder_id: Date.now(),
                            folder_name: 'New Folder',
                            parent_folder_id,
                            children: []  // Initialize an empty children array for the new folder
                        };
                        
                        folders.push(newFolder);
                        structuredArray = folderStructure(folders);

                        renderFolders(parent_folder_id);
                    }

                    // Function to delete a folder
                    function deleteFolder(id) {
                        // Implement your logic to delete a folder
                        // For example, you can remove the last folder in the array for demonstration purposes
                        structuredArray.splice(id, 1);
                        renderFolders();
                    }

                    // Initial rendering
                    renderFolders();
                </script>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div><!-- End Modal Dialog Scrollable-->