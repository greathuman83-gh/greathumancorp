// SmartEditor2 사진 업로더 — FormData + fetch로 php/ UploadHandler 연동 (jQuery 제거)
(function () {
    'use strict';

    var ed_nonce = '';
    if (opener && opener.window && opener.window.nhn) {
        try {
            ed_nonce = opener.window.nhn.husky.SE2M_Configuration.SE2M_Accessibility.ed_nonce;
        } catch (e) {
            ed_nonce = '';
        }
    }

    var gnu = {
        url: './php/?_nonce=' + ed_nonce,
        dragArea: null,
        listEl: null,
        guideEl: null,
        filter: /^(image\/bmp|image\/gif|image\/jpg|image\/jpeg|image\/png)$/i,
        files: [],
        file_limit: 10,
        imgw: 100,
        imgh: 70,
        file_api_support: !!(window.ProgressEvent && window.FileReader),

        init: function () {
            this.dragArea = document.getElementById('drag_area');
            this.listEl = document.querySelector('#drag_area > ul');
            this.guideEl = document.getElementById('guide_text');
            if (this.file_api_support && this.guideEl) {
                this.guideEl.classList.remove('hidebg');
                this.guideEl.classList.add('showbg');
            }
        },

        file_push: function (file) {
            this.files.push(file);
        },

        listCount: function () {
            return this.listEl ? this.listEl.querySelectorAll('li.sort_list').length : 0;
        },

        _readymodebg: function () {
            if (!this.file_api_support || !this.guideEl) return;
            this.guideEl.classList.remove('hidebg');
            this.guideEl.classList.add('showbg');
        },

        _startmodebg: function () {
            if (!this.file_api_support || !this.guideEl) return;
            this.guideEl.classList.remove('showbg');
            this.guideEl.classList.add('hidebg');
        },

        get_ratio: function (width, height) {
            var ratio = 0;
            var ret_img = {};
            if (!width || !height) {
                ret_img.width = this.imgw;
                ret_img.height = this.imgh;
                return ret_img;
            }
            if (width > this.imgw) {
                ratio = this.imgw / width;
                height = height * ratio;
                width = this.imgw;
            }
            if (height > this.imgh) {
                ratio = this.imgh / height;
                width = width * ratio;
                height = this.imgh;
            }
            ret_img.width = parseInt(width, 10);
            ret_img.height = parseInt(height, 10);
            return ret_img;
        },

        // 목록 행 생성 — 업로드 전/후 공통
        createListItem: function (fileName) {
            var li = document.createElement('li');
            li.className = 'sort_list';

            var wrap = document.createElement('div');
            var thumb = document.createElement('img');
            thumb.src = './img/loading.gif';
            thumb.className = 'pre_thumb';
            wrap.appendChild(thumb);
            wrap.appendChild(document.createElement('br'));

            var nameSpan = document.createElement('span');
            nameSpan.textContent = fileName;
            wrap.appendChild(nameSpan);

            var delSpan = document.createElement('span');
            delSpan.className = 'delete_img';
            delSpan.setAttribute('data-delete', fileName);
            delSpan.setAttribute('data-url', '');
            delSpan.innerHTML = "<img src='./img/system_delete.png' alt='삭제' title='삭제'>";
            delSpan.addEventListener('click', this._delete.bind(this));
            wrap.appendChild(delSpan);

            li.appendChild(wrap);
            this.listEl.appendChild(li);
            return li;
        },

        _delete: function (e) {
            e.preventDefault();
            var button = e.currentTarget;
            var delete_url = button.getAttribute('data-delete');
            var othis = this;
            if (delete_url) {
                fetch(othis.url + '&del=1&file=' + encodeURIComponent(delete_url)).catch(function () {});
            }
            var li = button.closest('li.sort_list');
            if (li) {
                li.remove();
            }
            if (!othis.listCount()) {
                othis._readymodebg();
            }
        },

        // 서버 JSON files[] 항목으로 썸네일·사이즈 갱신
        applyUploadResult: function (li, file) {
            var wrap = li.querySelector('div');
            if (!wrap) return;

            if (file.url && !file.error) {
                var ret = this.get_ratio(file.width, file.height);
                var size_text = (file.width || '') + ' x ' + (file.height || '');
                var thumb = wrap.querySelector('img.pre_thumb');
                if (thumb) {
                    thumb.src = file.url;
                    thumb.width = ret.width;
                    thumb.height = ret.height;
                }
                var del = wrap.querySelector('.delete_img');
                if (del) {
                    del.setAttribute('data-delete', file.name);
                    del.setAttribute('data-url', file.url);
                }
                var sizeSpan = document.createElement('span');
                sizeSpan.textContent = size_text;
                wrap.appendChild(document.createElement('br'));
                wrap.appendChild(sizeSpan);
                this.file_push(file);
            } else if (file.error) {
                var err = document.createElement('span');
                err.className = 'text-danger';
                err.textContent = file.error;
                wrap.appendChild(document.createElement('br'));
                wrap.appendChild(err);
            }
        },

        // 단일 파일 POST — UploadHandler param_name=files
        uploadFile: function (file, li) {
            var othis = this;
            var formData = new FormData();
            formData.append('files[]', file);

            return fetch(othis.url, {
                method: 'POST',
                body: formData
            })
                .then(function (res) {
                    return res.json();
                })
                .then(function (result) {
                    var uploaded = (result && result.files) ? result.files : [];
                    if (!uploaded.length) {
                        throw new Error('empty');
                    }
                    othis.applyUploadResult(li, uploaded[0]);
                })
                .catch(function () {
                    var wrap = li.querySelector('div');
                    if (!wrap) return;
                    var err = document.createElement('span');
                    err.className = 'text-danger';
                    err.textContent = 'File upload failed.';
                    wrap.appendChild(document.createElement('br'));
                    wrap.appendChild(err);
                });
        },

        // 파일 선택·드롭 — 순차 업로드 (file_limit 이하)
        handleFiles: function (fileList) {
            var othis = this;
            var files = Array.prototype.slice.call(fileList || []);
            if (!files.length) return;

            var remain = othis.file_limit - othis.listCount();
            if (remain <= 0) {
                alert('이미지를 한번에 ' + othis.file_limit + '개 이하로 선택해주세요.');
                return;
            }
            if (files.length > remain) {
                alert('이미지를 한번에 ' + othis.file_limit + '개 이하로 선택해주세요.');
                files = files.slice(0, remain);
            }

            othis._startmodebg();

            var chain = Promise.resolve();
            files.forEach(function (file) {
                chain = chain.then(function () {
                    if (!othis.filter.test(file.type)) {
                        alert('이미지만 허용합니다.');
                        return;
                    }
                    var li = othis.createListItem(file.name);
                    return othis.uploadFile(file, li);
                });
            });
            return chain;
        },

        setPhotoToEditor: function (oFileInfo) {
            if (opener && opener.nhn && opener.nhn.husky && opener.nhn.husky.PopUpManager) {
                opener.nhn.husky.PopUpManager.setCallback(window, 'SET_PHOTO', [oFileInfo]);
            }
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        gnu.init();

        var fileInput = document.getElementById('fileupload');
        if (fileInput) {
            fileInput.addEventListener('change', function () {
                gnu.handleFiles(this.files);
                this.value = '';
            });
        }

        // 드래그 앤 드롭 업로드
        if (gnu.dragArea) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function (evt) {
                gnu.dragArea.addEventListener(evt, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });
            gnu.dragArea.addEventListener('drop', function (e) {
                if (!gnu.file_api_support) {
                    alert('브라우저가 드래그 앤 드랍을 지원하지 않습니다.');
                    return;
                }
                var dt = e.dataTransfer;
                if (dt && dt.files) {
                    gnu.handleFiles(dt.files);
                }
            });
        }

        var allRemoveBtn = document.getElementById('all_remove_btn');
        if (allRemoveBtn) {
            allRemoveBtn.addEventListener('click', function (e) {
                e.preventDefault();
                if (!gnu.listCount()) return;
                if (!confirm('추가한 이미지가 있습니다.정말 삭제 하시겠습니까?')) return;
                gnu.listEl.querySelectorAll('.delete_img').forEach(function (btn) {
                    btn.click();
                });
            });
        }

        // 등록 — 에디터 SET_PHOTO 콜백용 aResult 구조 유지
        var submitBtn = document.getElementById('img_upload_submit');
        if (submitBtn) {
            submitBtn.addEventListener('click', function (e) {
                e.preventDefault();
                var aResult = [];
                gnu.listEl.querySelectorAll('.delete_img').forEach(function (el) {
                    if (!el.getAttribute('data-url')) return;
                    aResult.push({
                        bNewLine: 'true',
                        sAlign: '',
                        sFileName: el.getAttribute('data-delete'),
                        sFileURL: el.getAttribute('data-url')
                    });
                });
                if (aResult.length) {
                    gnu.setPhotoToEditor(aResult);
                }
                window.close();
            });
        }

        var closeBtn = document.getElementById('close_w_btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', function (e) {
                e.preventDefault();
                window.close();
            });
        }
    });
})();
