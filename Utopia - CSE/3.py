# Original task: Validate if two knights in a game of chess can attack each other based on their position.
import unittest
from typing import Union


class ChessPosition:
    # We need math, thus we will need to convert letters to numbers to do it
    LettersToNumbers = {
        'A': 1,
        'B': 2,
        'C': 3,
        'D': 4,
        'E': 5,
        'F': 6,
        'G': 7,
        'H': 8,
    }

    # I know it's counterintuitive, but we do need math
    letter: int
    number: int

    def __init__(self, number: int, letter: Union[str, int]):
        # Check if valid letter (or respective number)
        # Number is used by "internal" calls, since we need number for calculations
        # For letter we also use upper() for consistency and user convenience
        if type(letter) == str and letter.upper() in self.LettersToNumbers:
            self.letter = self.LettersToNumbers[letter.upper()]
        else:
            if type(letter) == int and 1 <= abs(letter) <= 8:
                self.letter = abs(letter)
            else:
                raise ValueError('Unsupported letter `' + str(letter) + '`')
        # Check if valid number
        if 1 <= abs(number) <= 8:
            self.number = abs(number)
        else:
            raise ValueError('Unsupported number `' + str(number) + '`')

    # Need this magic for comparison of objects
    def __eq__(self, other):
        return self.number == other.number and self.letter == other.letter


# I would prefer to have a class describing the Knight, although it may be a bit excessive for the task
class Knight:
    # All possible moves of a knight (independent of position)
    KnightMoves = (
        {'X': 2, 'Y': 1},
        {'X': 1, 'Y': 2},
        {'X': -1, 'Y': 2},
        {'X': -2, 'Y': 1},
        {'X': -2, 'Y': -1},
        {'X': -1, 'Y': -2},
        {'X': 1, 'Y': -2},
        {'X': 2, 'Y': -1},
    )

    # Initialize along with getting all possible moves based on current position (not accounting for other chess pieces)
    def __init__(self, position: ChessPosition):
        self.moves = self.get_moves(position)

    # We will need to get possible moves. Ideally I would
    def get_moves(self, position: ChessPosition) -> list:
        moves = []
        for move in self.KnightMoves:
            if 1 <= position.letter + move['X'] <= 8 and 1 <= position.number + move['Y'] <= 8:
                moves.append(ChessPosition(position.number + move['Y'], position.letter + move['X']))
        return moves


def can_attack(chess_position_1: ChessPosition, chess_position_2: ChessPosition) -> bool:
    # Since we are dealing with 2 identical pieces, it's enough to get mlist of moves for 1 of them,
    # And then check if position of the 2nd piece is in the list
    return chess_position_2 in Knight(chess_position_1).moves


class TestCanAttack(unittest.TestCase):

    def test_can_attack(self):

        self.assertTrue(
            can_attack(ChessPosition(2, "C"), ChessPosition(4, "D"))
        )

        self.assertTrue(
            can_attack(ChessPosition(7, "F"), ChessPosition(5, "E"))
        )

        self.assertTrue(
            can_attack(ChessPosition(2, "C"), ChessPosition(1, "A"))
        )

        self.assertTrue(
            can_attack(ChessPosition(6, "A"), ChessPosition(4, "B")),
        )

        self.assertFalse(
            can_attack(ChessPosition(6, "A"), ChessPosition(5, "B")),
        )

        self.assertFalse(
            can_attack(ChessPosition(2, "C"), ChessPosition(2, "C")),
        )

        self.assertFalse(
            # Added abs() in order to handle cases like this
            can_attack(ChessPosition(-1, "A"), ChessPosition(1, "B")),
        )

        self.assertFalse(
            can_attack(ChessPosition(4, "D"), ChessPosition(5, "E")),
        )


if __name__ == '__main__':
    unittest.main()
