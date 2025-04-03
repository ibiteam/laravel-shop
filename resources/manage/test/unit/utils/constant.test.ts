import { expect, test } from 'vitest'
import constants from '@/utils/constants';

test('is success', () => {
    const code = 200
    expect(constants.isSuccess(code)).toBe(true)
})

test('is unauthorized', () => {
    const code = 401
    expect(constants.isUnauthorized(code)).toBe(true)
})

