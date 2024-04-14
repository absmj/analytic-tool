
const folderExplorer = {
    folders: new Map(folders.map((obj, key) => {
        return [key, obj];
    })),
    actions: true,
    structure: [],
    errorElement: document.getElementById("folder-error"),
    successElement: document.getElementById("folder-success"),
    expanded: new Set(),

    get selected() {
        const radio = document.querySelector("[name='folder']:checked")
        const text = new Set();
        const value = (v = radio.value) => {
            for (let [key, folder] of this.folders) {    
                if (folder['folder_id'] == v) {
                    text.add(folder['folder_name']);

                    if(folder['parent_folder_id']) {
                        const child = value(folder['parent_folder_id'])
                        text.add(folder['folder_name']);
                    }
                }
            }
            
            return [...text.values()].reverse().join(" > ");
        }

        return {
            id: radio.value,
            text: value()
        }
    },

    async tryOrCatch(tryCallback, catchCallback = null) {
        try {
            uiInterface.loading = true
            await tryCallback()
        } catch (e) {
            if (!catchCallback) {
                uiInterface.error = e.responseText ?? e.status ?? e.message;
            }
            else
                catchCallback(e)
            console.error(e)
        } finally {
            uiInterface.loading = false
        }
    },

    set error(e) {
        if (!e) {
            uiInterface.error = e
        } else {
            console.log('emp')
        }
    },


    set success(e) {
        uiInterface.success = e
    },

    get folder() {
        return this.structure.filter(f => f.isActive)
    },

    get defaultFolderName() {
        const defaultNames = [...this.folders.values()].filter(f => /Yeni qovluq(\s*\d+)*/gmi.test(f.folder_name))
        return 'Yeni qovluq' + (defaultNames.length ? ' ' + defaultNames.length : '')
    },

    set folder({
        key,
        folder
    }) {
        this.folders.set(key, folder)
        this.expanded.add(folder.parent_folder_id)
        this.state = this.folderStructure()
        this.render();
    },

    folderStructure(parent = null) {

        const result = new Map();
        for (let [key, folder] of this.folders) {
            if (folder['parent_folder_id'] == parent) {
                children = this.folderStructure(folder['folder_id']);

                if (children) {
                    folder['children'] = children;
                }

                result.set(key, folder);
            }
        }

        return result;
    },

    generateHTML(folder, key) {
        const li = document.createElement('li');
        li.id = folder.folder_id;

        const folderEl = document.createElement("div")
        folderEl.classList.add("d-flex", "justify-content-between")
        folderEl.dataset.target = key

        const folderName = document.createElement("div");
        folderName.classList.add("form-check")

        folderName.innerHTML = `
                                    <input class='form-check-input' type='radio' name='folder' value='${folder.folder_id}' />
                                    <i class="bi bi-folder-fill"></i>
                                    ${folder.folder_name + ' (' + (folder.children ? folder.children.size : 0) + ')'}`;

        if(this.actions) {
            const operation = document.createElement("fieldset")
            operation.classList.add("operation")
            operation.dataset.id = folder.folder_id
            const createFolder = document.createElement("i")
            const deleteFolder = document.createElement("i")
            const renameFolder = document.createElement("i")
            createFolder.classList.add("text-primary", "me-2", "bi", "bi-folder-plus")
            renameFolder.classList.add("text-dark", "me-2", "bi", "bi-cursor-text")
            deleteFolder.classList.add("text-danger", "me-2", "bi", "bi-folder-minus")
            createFolder.onclick = () => this.create(folder.folder_id)
            deleteFolder.onclick = () => this.delete(key)
            renameFolder.onclick = () => this.rename(key)
            operation.appendChild(createFolder)
            operation.appendChild(renameFolder)
            operation.appendChild(deleteFolder)
            folderEl.appendChild(folderName)
            folderEl.appendChild(operation)
        } else {
            folderEl.appendChild(folderName)
        }


        li.appendChild(folderEl)

        li.onclick = (e) => {
            if (e.target?.offsetParent?.id == folder.folder_id) {
                this.expanded.add(folder.folder_id)
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
            folder.children.forEach((child, k) => {
                ul.appendChild(this.generateHTML(child, k));
            });
            li.appendChild(ul);
        }

        if (this.expanded.has(folder.folder_id)) {
            li.classList.add("expanded")
        }

        return li;
    },

    async create(parent_folder_id = null) {

        this.tryOrCatch(async () => {
            const folder_name = prompt("Zəhmət olmasa, yeni qovluğun adını daxil edin", this.defaultFolderName);
            if (!folder_name) return;


            const newFolder = {
                folder_name,
                parent_folder_id,
                children: [] // Initialize an empty children array for the new folder
            };

            const request = await $.post("/folders/create", {
                folder_name: newFolder.folder_name,
                parent_folder_id: newFolder.parent_folder_id
            })
            newFolder.folder_id = request.folder_id
            this.folder = {
                key: this.folders.size,
                folder: newFolder
            }
            this.success = "Qovluq yaradıldı"
        }, (e) => {
            this.error = 'Qovluğun yaradılması zaman xəta baş verdi! ' + (e.message)
        })
    },

    // Function to delete a folder
    async delete(id) {
        if (!confirm("Əminsinizmi?")) return;

        this.tryOrCatch(async () => {
            const folder = this.folders.get(id);
            const request = await $.post("/folders/delete", {
                folder_id: folder.folder_id
            })
            this.folders.delete(id);
            this.structure = this.folderStructure()
            this.render()
        }, (e) => {
            this.error = 'Qovluğun silinməsi zaman xəta baş verdi! ' + (e.message)
        })

    },

    async rename(id) {
        this.tryOrCatch(async () => {
            const folder = this.folders.get(id)
            const newName = prompt("Zəhmət olmasa, qovluğun yeni adını daxil edin", folder.folder_name)
            folder.folder_name = newName
            const request = await $.post("/folders/update", {
                folder_id: folder.folder_id,
                folder_name: newName
            })
            this.folder = {
                key: id,
                folder
            }
            this.success = "Qovluğun adını dəyişdirildi"
        }, (e) => {
            this.error = 'Qovluğun adını dəyişdirən zaman xəta baş verdi! ' + (e.message)
        })
    },

    render(el = 'folderTree') {

        const folderTree = document.getElementById(el)
        folderTree.innerHTML = ''


        this.structure.forEach((folder, key) => {
            folderTree.appendChild(this.generateHTML(folder, key));
        });
    },

    update() {},

    init(el = 'folderTree', actions = true) {
        console.log(el)
        this.structure = this.folderStructure();
        this.actions = actions
        this.render(el);
    }
}