// stores/articles.ts
import { defineStore } from 'pinia'

interface Article {
  id:              string
  title:           string
  slug:            string
  excerpt:         string | null
  content:         string
  status:          'draft' | 'published' | 'archived'
  cover_image_url: string | null
  tags:            string[] | null
  views_count:     number
  likes_count:     number
  is_liked:        boolean
  reading_time:    number
  is_featured:     boolean
  published_at:    string | null
  author: {
    id:     string
    name:   string
    avatar: string | null
  }
  category: {
    id:    number
    name:  string
    slug:  string
    color: string
  } | null
  comments_count?: number
}

interface ArticleMeta {
  total:        number
  per_page:     number
  current_page: number
  last_page:    number
}

interface ArticleState {
  list:       Article[]
  featured:   Article[]
  current:    Article | null
  meta:       ArticleMeta | null
  loading:    boolean
  error:      string | null
}

export const useArticleStore = defineStore('articles', {
  state: (): ArticleState => ({
    list:     [],
    featured: [],
    current:  null,
    meta:     null,
    loading:  false,
    error:    null,
  }),

  getters: {
    hasNextPage: (state) =>
      state.meta ? state.meta.current_page < state.meta.last_page : false,
  },

  actions: {
    async fetchArticles(params?: Record<string, any>) {
      this.loading = true
      this.error   = null
      try {
        const api      = useApi()
        const response = await api.get<{ data: Article[]; meta: ArticleMeta }>(
          '/articles', params
        )
        this.list = response.data
        this.meta = response.meta
      } catch (e: any) {
        this.error = typeof e === 'string' ? e : 'Erreur de chargement.'
      } finally {
        this.loading = false
      }
    },

    async fetchFeatured() {
      try {
        const api      = useApi()
        const response = await api.get<{ data: Article[] }>('/articles/featured')
        this.featured  = response.data
      } catch { /* silencieux */ }
    },

    async fetchBySlug(slug: string) {
      this.loading = true
      this.error   = null
      try {
        const api      = useApi()
        const response = await api.get<{ data: Article }>(`/articles/${slug}`)
        this.current   = response.data
      } catch (e: any) {
        this.error = 'Article introuvable.'
        throw e
      } finally {
        this.loading = false
      }
    },

    async createArticle(data: Partial<Article>) {
      const api      = useApi()
      const response = await api.post<{ data: Article; message: string }>('/articles', data)
      return response.data
    },

    async updateArticle(id: string, data: Partial<Article>) {
      const api      = useApi()
      const response = await api.put<{ data: Article; message: string }>(`/articles/${id}`, data)
      return response.data
    },

    async deleteArticle(id: string) {
      const api = useApi()
      await api.delete(`/articles/${id}`)
      this.list = this.list.filter((a) => a.id !== id)
    },

    async toggleLike(id: string) {
      const api      = useApi()
      const response = await api.post<{ likes_count: number; is_liked: boolean }>(
        `/articles/${id}/like`
      )
      // Mettre à jour en local
      const article = this.list.find((a) => a.id === id) ?? this.current
      if (article && article.id === id) {
        article.likes_count = response.likes_count
        article.is_liked    = response.is_liked
      }
    },
  },
})
