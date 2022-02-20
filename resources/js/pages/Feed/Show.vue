<template>
  <div class="mx-auto container p-6 max-w-2xl">
    <div class="rounded shadow bg-white">
      <div class="overflow-y-auto divide-y" style="height: 64rem" ref="commentsListNode">
        <div v-for="comment in localComments" class="p-6">
          <p class="font-medium text-sm text-gray-900">
            {{ comment.author }} (#{{ comment.id }})
          </p>
          <p class="text-sm text-gray-700 mt-1">
            {{ comment.content }}
          </p>
          <div class="mt-1">
            <button type="button" v-if="!comment.liked" @click="handleLike(comment)">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                />
              </svg>
            </button>
            <button type="button" v-if="comment.liked" @click="handleDislike(comment)">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-pink-400"
                viewBox="0 0 20 20"
                fill="currentColor"
              >
                <path
                  fill-rule="evenodd"
                  d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                  clip-rule="evenodd"
                />
              </svg>
            </button>
          </div>
          <p class="text-xs text-gray-400 mt-1"> 1 hour ago </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, onMounted, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { useForm } from '@inertiajs/inertia-vue3'
import Default from '../../layouts/Default'

export default defineComponent({
  layout: [Default],

  props: ['comments', 'updated_comment'],

  setup(props) {
    const localComments = ref(props.comments.data)
    const nextLink = ref(props.comments.links.next)
    const commentsListNode = ref(null)

    onMounted(() => {
        if (props.comments.meta.current_page !== 1) {
            window.location.href = props.comments.links.first
            return
        }

      commentsListNode.value.onscroll = () => {
        const node = commentsListNode.value

        const scrolledToBottom = node.offsetHeight + node.scrollTop >= node.scrollHeight

        if (!scrolledToBottom) {
          return
        }

        Inertia.visit(nextLink.value, {
          preserveState: true,
          preserveScroll: true,
          onSuccess: (data) => {
            localComments.value = [...localComments.value, ...data.props.comments.data]
            nextLink.value = data.props.comments.links.next
          },
        })
      }
    })

    const handleLike = (comment) => {
        const form = useForm({
            current_page: props.comments.meta.current_page
        })

        form.post(`/comments/${comment.id}/like`, {
            only: ['updated_comment'],
            preserveState: true,
            preserveScroll: true,
            onSuccess: (data) => {
                handleUpdatedCommentChange(data.props.updated_comment.data)
            }
        })
    }

    const handleDislike = (comment) => {
        const form = useForm({
            current_page: props.comments.meta.current_page
        })

        form.delete(`/comments/${comment.id}/like`, {
            only: ['updated_comment'],
            preserveState: true,
            preserveScroll: true,
            onSuccess: (data) => {
                handleUpdatedCommentChange(data.props.updated_comment.data)
            }
        })
    }

    const handleUpdatedCommentChange = (updatedComment) => {
        localComments.value = localComments.value.map(comment => {
            if (comment.id !== updatedComment.id) {
                return comment
            }

            return updatedComment
        })
    }

    return {
      localComments,
      nextLink,
      commentsListNode,
      handleLike,
      handleDislike,
    }
  },
})
</script>
