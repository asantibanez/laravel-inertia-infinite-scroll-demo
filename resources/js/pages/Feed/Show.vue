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
          <p class="text-xs text-gray-400 mt-1"> 1 hour ago </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, onMounted, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import Default from '../../layouts/Default'

export default defineComponent({
  layout: [Default],

  props: ['comments', 'next_comments'],

  setup(props) {
    const localComments = ref(props.comments.data)
    const nextLink = ref(props.comments.links.next)
    const commentsListNode = ref(null)

    onMounted(() => {
      commentsListNode.value.onscroll = () => {
        const node = commentsListNode.value

        const scrolledToBottom = node.offsetHeight + node.scrollTop >= node.scrollHeight

        if (!scrolledToBottom) {
          return
        }

        Inertia.visit(nextLink.value, {
          only: ['next_comments'],
          preserveState: true,
          preserveScroll: true,
          onSuccess: (data) => {
            localComments.value = [...localComments.value, ...data.props.next_comments.data]

            nextLink.value = data.props.next_comments.links.next
          },
        })
      }
    })

    return {
      localComments,
        nextLink,
      commentsListNode,
    }
  },
})
</script>
