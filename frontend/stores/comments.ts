import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCommentStore = defineStore('comments', () => {
  const list = ref<any[]>([])
  const loading = ref(false)

  // Fetch all comments for an article
  async function fetchComments(articleId: string) {
    loading.value = true
    try {
      const response: any = await $fetch(`/api/articles/${articleId}/comments`)
      list.value = response.data ?? []
    } finally {
      loading.value = false
    }
  }

  // Root comments (no parent)
  const rootComments = computed(() => list.value.filter(c => !c.parent_id))

  // Replies for a given parent comment ID
  function replies(parentId: string) {
    return list.value.filter(c => c.parent_id === parentId)
  }

  // Add a new comment (or reply)
  async function addComment(articleId: string, payload: { content: string; parent_id?: string | null }) {
    await $fetch(`/api/articles/${articleId}/comments`, {
      method: 'POST',
      body: payload,
    })
    // Refresh list after posting
    await fetchComments(articleId)
  }

  // Toggle like on a comment (requires backend endpoint /api/comments/{id}/like)
  async function toggleLike(commentId: string) {
    await $fetch(`/api/comments/${commentId}/like`, { method: 'POST' })
    const comment = list.value.find(c => c.id === commentId)
    if (comment) {
      comment.is_liked = !comment.is_liked
      comment.likes_count = (comment.likes_count ?? 0) + (comment.is_liked ? 1 : -1)
    }
  }

  return {
    list,
    loading,
    fetchComments,
    rootComments,
    replies,
    addComment,
    toggleLike,
  }
})
