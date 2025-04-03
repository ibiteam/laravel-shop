import { expect, test } from 'vitest'
import { isSuccess,isUnauthorized } from '@/utils/constants';

test('is success', () => {
    const code = 200
    expect(isSuccess(code)).toBe(true)
})

test('is unauthorized', () => {
    const code = 401
    expect(isUnauthorized(code)).toBe(true)
})

