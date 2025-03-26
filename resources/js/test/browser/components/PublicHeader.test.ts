import { expect, test } from 'vitest'
import { render } from 'vitest-browser-vue'
import PublicHeader from '@/components/PublicHeader.vue'

test('components public header', async () => {
    const { getByText } = render(PublicHeader, {
        props: { title: 'laravel shop' },
    })

    await expect.element(getByText('laravel shop')).toBeInTheDocument()
})
