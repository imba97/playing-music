<template>
  <div h-full w-full of-hidden>
    <div h-full w-full flex="~ col" items-center justify-center>
      <div p-10 lt-md="w-[90%]" md="w-[60%]" flex="~ col" items-center justify-center gap-6 bg="[rgba(0,0,0,0.5)]"
        rounded-10>
        <div text="white lt-md:4 md:8">
          {{ playing ? '我正在听' : '当前没在听歌，最近听了' }}
        </div>
        <div lt-md="h-48 w-48" md="h-86 w-86" rounded-full of-hidden>
          <img v-show="imageLoaded" :src="music.image" h-full w-full object-cover :class="playing ? 'animate-spin animate-duration-30000' : ''
            " />
          <div v-show="!imageLoaded" i-ph-music-note-simple-duotone bg-gray relative left-12 h="80%" w="80%"></div>
        </div>
        <Text text="white center lt-md:6 md:12" w-full>{{ music.name }}</Text>
      </div>
    </div>

    <div fixed top="-10%" left="-10%" h="120%" w="120%" z--1 blur-32>
      <img v-show="imageLoaded" :src="music.image" h-full w-full object-cover select-none />
      <div v-show="!imageLoaded" i-ph-music-note-simple-duotone bg-gray h-full w-full></div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, reactive, onMounted } from 'vue';

import { get, find } from 'lodash'
import axios from 'axios'

import Text from '@/components/Text.vue'

const playing = ref(false)
const imageLoaded = ref(false)

const music = reactive({
  name: '加载中',
  image: ''
})

onMounted(() => {
  getMusic()

  // 10 秒获取一次
  setInterval(() => {
    getMusic()
  }, 10000)
})

const getMusic = async () => {
  const response = await axios.get(import.meta.env.VITE_APP_API)

  if (!response || !response.data) {
    return
  }

  playing.value = get(response.data, '@attr.nowplaying') === 'true'

  music.name = get(response.data, 'name')

  const findedImage = find(get(response.data, 'image'), { size: 'extralarge' })

  if (findedImage) {
    const image = new Image()
    image.src = findedImage['#text']
    image.onload = () => {
      imageLoaded.value = true
      music.image = findedImage['#text']
    }
  }
}
</script>
