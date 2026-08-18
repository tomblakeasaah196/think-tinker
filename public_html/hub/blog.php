<?php $pageTitle='Blog'; $currentModule='blog'; require_once __DIR__.'/../templates/header-hub.php'; ?>
<div class="page-header"><h1>Blog Manager</h1><div class="actions"><button class="btn btn-primary btn-sm" onclick="showPostEditor()">+ New Post</button></div></div>

<!-- Post List -->
<div id="postListView">
    <div class="filter-bar">
        <select class="form-select" id="postStatus" onchange="loadPosts()"><option value="">All</option><option value="published">Published</option><option value="draft">Drafts</option></select>
    </div>
    <div class="hub-card"><div class="hub-card-body no-pad"><table class="hub-table"><thead><tr><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th>Date</th><th></th></tr></thead><tbody id="postsTable"></tbody></table></div></div>
    <div class="pagination" id="postsPagination"></div>
</div>

<!-- Post Editor -->
<div id="postEditorView" style="display:none;">
    <button class="btn btn-ghost btn-sm mb-3" onclick="hidePostEditor()">← Back to list</button>
    <div class="hub-card">
        <div class="hub-card-header"><h3 id="editorTitle">New Blog Post</h3></div>
        <div class="hub-card-body">
            <form id="postForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="create_post">
                <input type="hidden" name="id" value="">
                <div class="form-group"><label class="form-label">Title *</label><input type="text" name="title" class="form-input" required></div>
                <div class="form-row">
                    <div class="form-group"><label class="form-label">Category</label>
                        <select name="category" class="form-select"><option value="announcements">Announcements</option><option value="parenting">Parenting Tips</option><option value="education">Education</option><option value="stem">STEM Activities</option><option value="events">Events</option></select>
                    </div>
                    <div class="form-group"><label class="form-label">Tags (comma-separated)</label><input type="text" name="tags" class="form-input" placeholder="e.g. reading, stem, tips"></div>
                </div>
                <div class="form-group"><label class="form-label">Featured Image</label><input type="file" name="featured_image" class="form-input" accept="image/*"></div>
                <div class="form-group"><label class="form-label">Excerpt</label><textarea name="excerpt" class="form-textarea" rows="2" placeholder="Short summary (auto-generated if blank)"></textarea></div>
                <div class="form-group">
                    <label class="form-label">Content *</label>
                    <textarea name="body" id="postBody" class="form-textarea" rows="16" style="font-family: monospace; font-size: 0.875rem;"></textarea>
                    <div class="form-hint">Supports HTML. Paste from a rich-text editor or write directly.</div>
                </div>
                <div class="flex gap-2" style="margin-top: 16px;">
                    <button type="button" class="btn btn-primary" onclick="savePost('published')">Publish</button>
                    <button type="button" class="btn btn-outline" onclick="savePost('draft')">Save as Draft</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadPosts(){
    Hub.loadTable('BlogController.php','get_posts',{admin:1,status:$('#postStatus').val(),page:1},function(p){
        const sc={published:'badge-green',draft:'badge-orange'}[p.status]||'badge-gray';
        return `<tr><td><strong style="cursor:pointer;color:var(--tinker-teal);" onclick="editPost(${p.id})">${TT.escHtml(p.title)}</strong></td>
        <td><span class="badge badge-navy">${p.category||'-'}</span></td><td class="text-sm">${TT.escHtml(p.author_name)}</td>
        <td><span class="badge ${sc}">${p.status}</span></td><td class="text-sm">${p.formatted_date}</td>
        <td class="row-actions">
            <button class="btn btn-ghost btn-sm" onclick="editPost(${p.id})">Edit</button>
            ${p.status==='draft'?`<button class="btn btn-success btn-sm" onclick="publishPost(${p.id})">Publish</button>`:''}
            <button class="btn btn-ghost btn-sm" onclick="deletePost(${p.id})">Delete</button>
        </td></tr>`;
    },'#postsTable','#postsPagination');
}

function showPostEditor(id){
    TT.clearForm('#postForm');
    $('[name=action]','#postForm').val('create_post');
    $('[name=id]','#postForm').val('');
    $('#editorTitle').text('New Blog Post');
    $('#postListView').hide();$('#postEditorView').show();
}
function hidePostEditor(){$('#postEditorView').hide();$('#postListView').show();loadPosts();}

function editPost(id){
    TT.get('BlogController.php',{action:'get_post',id}).done(function(r){
        if(!r.success)return;const p=r.data.post;
        $('[name=action]','#postForm').val('update_post');
        $('[name=id]','#postForm').val(p.id);
        $('[name=title]','#postForm').val(p.title);
        $('[name=category]','#postForm').val(p.category);
        $('[name=tags]','#postForm').val(p.tags_array?p.tags_array.join(', '):'');
        $('[name=excerpt]','#postForm').val(p.excerpt);
        $('#postBody').val(p.body);
        $('#editorTitle').text('Edit: '+p.title);
        $('#postListView').hide();$('#postEditorView').show();
    });
}

function savePost(status){
    $('<input>').attr({type:'hidden',name:'status',value:status}).appendTo('#postForm');
    TT.submitForm('#postForm','BlogController.php',{
        onSuccess:r=>{
            if(r.success){TT.toast(status==='published'?'Post published!':'Draft saved.','success');hidePostEditor();}
        }
    });
    $('[name=status]','#postForm').last().remove();
}

function publishPost(id){TT.api('BlogController.php',{action:'publish_post',id}).done(r=>{if(r.success){loadPosts();TT.toast('Published!','success');}});}
function deletePost(id){TT.modal.confirm('Delete this post permanently?',()=>{TT.api('BlogController.php',{action:'delete_post',id}).done(r=>{if(r.success){loadPosts();TT.toast('Post deleted.','success');}});});}

$(function(){loadPosts();
    // If URL has ?action=add, open editor
    if(new URLSearchParams(location.search).get('action')==='add') showPostEditor();
});
</script>
<?php require_once __DIR__.'/../templates/footer-hub.php'; ?>
